<?php
require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;

echo "=== CRM WebSocket 서버 시작 (Windows) ===\n";

// WebSocket 서버만 실행
$ws_worker = new Worker("websocket://0.0.0.0:8080");
$ws_worker->count = 1;
$ws_worker->name = 'CRM-WebSocket';

// 연결된 클라이언트들 저장
$connections = array();
$last_check_time = 0;

// 클라이언트 연결시
$ws_worker->onConnect = function($connection) {
    global $connections;
    $connections[$connection->id] = $connection;
    echo "[" . date('Y-m-d H:i:s') . "] 새 클라이언트 연결: {$connection->id}\n";
    
    // 연결 확인 메시지 전송
    $connection->send(json_encode([
        'type' => 'connected',
        'message' => 'CRM 실시간 알림 서비스에 연결되었습니다.',
        'timestamp' => date('Y-m-d H:i:s')
    ]));
};

// 메시지 수신시 (클라이언트에서 보낸 메시지)
$ws_worker->onMessage = function($connection, $data) {
    global $connections, $last_check_time;
    
    echo "[" . date('Y-m-d H:i:s') . "] 클라이언트 메시지: {$data}\n";
    
    // 메시지 받을 때마다 알림 파일 체크 (Timer 대신)
    $current_time = time();
    if ($current_time - $last_check_time >= 1) { // 1초마다 체크
        checkForNewNotifications();
        $last_check_time = $current_time;
    }
    
    $message = json_decode($data, true);
    if ($message && isset($message['type'])) {
        switch ($message['type']) {
            case 'ping':
                $connection->send(json_encode(['type' => 'pong', 'timestamp' => date('Y-m-d H:i:s')]));
                // ping 받을 때도 알림 체크
                checkForNewNotifications();
                break;
                
            case 'notify_all':
                // 다른 모든 클라이언트에게 메시지 전달
                foreach($connections as $conn) {
                    if ($conn->id !== $connection->id) {
                        $conn->send(json_encode([
                            'type' => 'broadcast',
                            'message' => $message['message'] ?? 'Broadcast message',
                            'from' => $connection->id,
                            'timestamp' => date('Y-m-d H:i:s')
                        ]));
                    }
                }
                break;
                
            case 'check_notifications':
                // 수동으로 알림 체크 요청
                checkForNewNotifications();
                break;
        }
    }
};

// 연결 종료시
$ws_worker->onClose = function($connection) {
    global $connections;
    unset($connections[$connection->id]);
    echo "[" . date('Y-m-d H:i:s') . "] 클라이언트 연결 종료: {$connection->id}\n";
};

function checkForNewNotifications() {
    global $connections;
    
    // 알림 플래그 파일 체크 (예시)
    $notification_file = __DIR__ . '/notifications.json';
    if (file_exists($notification_file)) {
        $notifications = json_decode(file_get_contents($notification_file), true);
        
        if (!empty($notifications)) {
            foreach ($notifications as $notification) {
                // 모든 클라이언트에게 알림 전송
                foreach($connections as $conn) {
                    $conn->send(json_encode($notification));
                }
                
                echo "[" . date('Y-m-d H:i:s') . "] 알림 전송: " . json_encode($notification, JSON_UNESCAPED_UNICODE) . "\n";
            }
            
            // 전송 후 파일 삭제
            unlink($notification_file);
        }
    }
}

// 에러 처리
Worker::$stdoutFile = __DIR__ . '/workerman.log';

// 서버 정보 출력
echo "WebSocket 서버: ws://localhost:8080\n";
echo "로그 파일: " . __DIR__ . "/workerman.log\n";
echo "서버를 중지하려면 Ctrl+C를 누르세요.\n\n";

// 워커 실행
Worker::runAll();