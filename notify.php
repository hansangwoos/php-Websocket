<?php
// notify.php - Windows용 알림 전송 헬퍼 (디버깅 버전)

// 디버깅용 로그 함수
function debug_log($message) {
    error_log("[NOTIFY DEBUG] " . $message . "\n", 3, __DIR__ . '/debug.log');
}

function sendNotification($data) {
    $notification_file = __DIR__ . '/notifications.json';
    
    debug_log("sendNotification 호출됨: " . json_encode($data));
    
    // 기존 알림들 읽기
    $notifications = [];
    if (file_exists($notification_file)) {
        $notifications = json_decode(file_get_contents($notification_file), true) ?: [];
    }
    
    // 새 알림 추가
    $notifications[] = $data;
    
    // 파일에 저장
    file_put_contents($notification_file, json_encode($notifications));
    
    debug_log("알림 저장 완료, 총 " . count($notifications) . "개");
    
    return "알림이 대기열에 추가되었습니다.";
}

// HTTP 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_log("POST 요청 받음");
    
    // Content-Type 확인
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    debug_log("Content-Type: " . $content_type);
    
    $input = file_get_contents('php://input');
    debug_log("Raw input: " . $input);
    
    // POST 데이터가 form-encoded인지 JSON인지 확인
    if (strpos($content_type, 'application/json') !== false) {
        // JSON 데이터
        $data = json_decode($input, true);
        debug_log("JSON 파싱 결과: " . var_export($data, true));
    } else {
        // Form 데이터 처리
        debug_log("Form data 처리: " . var_export($_POST, true));
        
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            
            switch ($action) {
                case 'test_notification':
                    $data = [
                        'type' => 'new_ticket',
                        'message' => 'PHP Form에서 보낸 테스트 기표입니다!',
                        'data' => [
                            'ticket_id' => 'FORM_TEST_' . time(),
                            'agency' => 'PHP Form 테스트 에이전시',
                            'amount' => 1500000,
                            'currency' => 'KRW',
                            'sent_from' => 'PHP Form'
                        ],
                        'timestamp' => date('c')
                    ];
                    break;
                    
                case 'simulate_ticket':
                    $priority = $_POST['priority'] ?? '일반';
                    $data = [
                        'type' => 'new_ticket',
                        'message' => "PHP Form에서 보낸 {$priority} 기표입니다!",
                        'data' => [
                            'ticket_id' => 'FORM_TICKET_' . time(),
                            'agency' => $priority === '긴급' ? 'PHP Form 긴급 에이전시' : 'PHP Form 일반 에이전시',
                            'priority' => $priority,
                            'amount' => rand(500000, 5000000),
                            'currency' => 'KRW',
                            'customer_name' => 'Form고객' . rand(1, 1000),
                            'created_at' => date('c'),
                            'sent_from' => 'PHP Form'
                        ],
                        'timestamp' => date('c')
                    ];
                    break;
                    
                case 'custom_message':
                    $message = $_POST['custom_message'] ?? '';
                    $data = [
                        'type' => 'custom',
                        'message' => $message,
                        'data' => [
                            'sent_from' => 'PHP Form Custom',
                            'user_input' => true
                        ],
                        'timestamp' => date('c')
                    ];
                    break;
                    
                default:
                    $data = null;
                    break;
            }
        } else {
            $data = null;
        }
    }
    
    if ($data) {
        $result = sendNotification($data);
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success',
            'message' => $result,
            'timestamp' => date('Y-m-d H:i:s'),
            'data_received' => $data
        ], JSON_UNESCAPED_UNICODE);
    } else {
        debug_log("데이터 파싱 실패");
        
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(400);
        echo json_encode([
            'error' => '잘못된 데이터',
            'debug_info' => [
                'method' => $_SERVER['REQUEST_METHOD'],
                'content_type' => $content_type,
                'post_data' => $_POST,
                'raw_input' => $input
            ]
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// GET 요청 처리 (상태 확인)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $status = [
        'status' => 'running',
        'notification_file_exists' => file_exists(__DIR__ . '/notifications.json'),
        'php_version' => phpversion(),
        'debug_log_exists' => file_exists(__DIR__ . '/debug.log'),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($status, JSON_UNESCAPED_UNICODE);
    exit;
}

// CLI에서 실행시 테스트 알림 전송
if (!empty($argv)) {
    $testData = [
        'type' => 'new_ticket',
        'message' => 'CLI에서 보낸 테스트 알림입니다!',
        'data' => [
            'ticket_id' => 'CLI_TEST_' . time(),
            'agency' => 'CLI 테스트',
            'amount' => 999999
        ],
        'timestamp' => date('c')
    ];
    
    echo sendNotification($testData) . "\n";
}
?>