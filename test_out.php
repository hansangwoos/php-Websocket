<?php
// test_out.php - 외부 에이전시 시뮬레이터
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏢 외부 에이전시 - 기표 발송 시스템</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        }
        .agency-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        .ticket-form {
            background: rgba(255,255,255,0.1);
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .form-group { 
            margin: 15px 0; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
            color: #fff;
        }
        .form-group input, .form-group select, .form-group textarea { 
            padding: 10px; 
            width: 100%; 
            border: none;
            border-radius: 5px;
            background: rgba(255,255,255,0.9);
            color: #333;
            box-sizing: border-box;
        }
        button { 
            padding: 12px 25px; 
            margin: 10px 5px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-danger { background: #dc3545; color: white; }
        
        button:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        
        .result-area {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            display: none;
        }
        .agency-info {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="agency-header">
            <h1>🏢 ABC 여행사</h1>
            <p>기표 발송 시스템 v2.0</p>
            <p><strong>에이전시 코드:</strong> ABC001 | <strong>연결 상태:</strong> <span id="connectionStatus">연결 중...</span></p>
        </div>

        <?php
        // POST 요청 처리
        if ($_POST) {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'send_ticket':
                    $result = sendTicketToCRM($_POST);
                    echo "<div class='result-area' style='display: block; background: rgba(40, 167, 69, 0.2);'>";
                    echo "<h4>✅ 기표 발송 완료</h4>";
                    echo "<p>결과: " . $result . "</p>";
                    echo "</div>";
                    break;
                    
                case 'send_bulk':
                    $count = $_POST['bulk_count'] ?? 5;
                    $result = sendBulkTickets($count);
                    echo "<div class='result-area' style='display: block; background: rgba(40, 167, 69, 0.2);'>";
                    echo "<h4>✅ 대량 기표 발송 완료</h4>";
                    echo "<p>결과: " . $result . "</p>";
                    echo "</div>";
                    break;
                    
                case 'send_emergency':
                    $result = sendEmergencyNotification($_POST['emergency_message'] ?? '긴급 상황 발생');
                    echo "<div class='result-area' style='display: block; background: rgba(220, 53, 69, 0.2);'>";
                    echo "<h4>🚨 긴급 알림 발송 완료</h4>";
                    echo "<p>결과: " . $result . "</p>";
                    echo "</div>";
                    break;
            }
        }
        
        // 기표 발송 함수
        function sendTicketToCRM($data) {
            $ticketData = [
                'type' => 'new_ticket',
                'message' => "새로운 {$data['priority']} 기표가 접수되었습니다! (고객: {$data['customer_name']})",
                'data' => [
                    'ticket_id' => 'TK' . date('Ymd') . '_' . rand(1000, 9999),
                    'agency' => 'ABC 여행사',
                    'agency_code' => 'ABC001',
                    'customer_name' => $data['customer_name'],
                    'departure' => $data['departure'],
                    'destination' => $data['destination'],
                    'travel_date' => $data['travel_date'],
                    'priority' => $data['priority'],
                    'amount' => (int)$data['amount'],
                    'currency' => 'KRW',
                    'payment_method' => $data['payment_method'],
                    'notes' => $data['notes'],
                    'created_at' => date('c'),
                    'sent_from' => 'ABC 여행사 시스템'
                ],
                'timestamp' => date('c')
            ];
            
            return sendToCRM($ticketData);
        }
        
        function sendBulkTickets($count) {
            $customers = ['김철수', '이영희', '박민수', '최지영', '정우성', '한예슬', '송중기', '김태희'];
            $routes = [
                ['서울', '부산'], ['서울', '제주'], ['부산', '서울'], ['제주', '서울'],
                ['서울', '대구'], ['서울', '광주'], ['인천', '부산'], ['대전', '부산']
            ];
            
            $results = [];
            for ($i = 0; $i < $count; $i++) {
                $customer = $customers[array_rand($customers)];
                $route = $routes[array_rand($routes)];
                
                $ticketData = [
                    'type' => 'new_ticket',
                    'message' => "대량 기표 처리 - {$customer} 고객 기표 접수",
                    'data' => [
                        'ticket_id' => 'BULK' . date('Ymd') . '_' . rand(1000, 9999),
                        'agency' => 'ABC 여행사',
                        'agency_code' => 'ABC001',
                        'customer_name' => $customer,
                        'departure' => $route[0],
                        'destination' => $route[1],
                        'travel_date' => date('Y-m-d', strtotime('+' . rand(1, 30) . ' days')),
                        'priority' => rand(1, 10) > 8 ? '긴급' : '일반',
                        'amount' => rand(50000, 500000),
                        'currency' => 'KRW',
                        'payment_method' => rand(1, 2) == 1 ? '카드' : '현금',
                        'created_at' => date('c'),
                        'sent_from' => 'ABC 여행사 대량처리'
                    ],
                    'timestamp' => date('c')
                ];
                
                $results[] = sendToCRM($ticketData);
                usleep(200000); // 0.2초 대기
            }
            
            return count($results) . "개 기표 발송 완료";
        }
        
        function sendEmergencyNotification($message) {
            $emergencyData = [
                'type' => 'emergency_alert',
                'message' => "🚨 긴급 알림: " . $message,
                'data' => [
                    'alert_id' => 'EMERGENCY_' . time(),
                    'agency' => 'ABC 여행사',
                    'agency_code' => 'ABC001',
                    'alert_level' => 'HIGH',
                    'message' => $message,
                    'created_at' => date('c'),
                    'sent_from' => 'ABC 여행사 긴급시스템'
                ],
                'timestamp' => date('c')
            ];
            
            return sendToCRM($emergencyData);
        }
        
        function sendToCRM($data) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost/crm_test/notify.php');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($response === false) {
                return "CRM 서버 연결 실패";
            }
            
            if ($http_code === 200) {
                return "CRM 서버로 전송 완료 - " . $response;
            } else {
                return "CRM 서버 오류 (HTTP {$http_code}) - " . $response;
            }
        }
        ?>

        <!-- 기표 발송 폼 -->
        <div class="ticket-form">
            <h3>🎫 새 기표 발송</h3>
            <form method="POST">
                <input type="hidden" name="action" value="send_ticket">
                
                <div class="form-group">
                    <label>고객명</label>
                    <input type="text" name="customer_name" value="김고객" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>출발지</label>
                        <select name="departure">
                            <option value="서울">서울</option>
                            <option value="부산">부산</option>
                            <option value="제주">제주</option>
                            <option value="대구">대구</option>
                            <option value="광주">광주</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>도착지</label>
                        <select name="destination">
                            <option value="부산">부산</option>
                            <option value="서울">서울</option>
                            <option value="제주">제주</option>
                            <option value="대구">대구</option>
                            <option value="광주">광주</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>여행일</label>
                        <input type="date" name="travel_date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>우선순위</label>
                        <select name="priority">
                            <option value="일반">일반</option>
                            <option value="긴급">긴급</option>
                            <option value="VIP">VIP</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>금액 (원)</label>
                        <input type="number" name="amount" value="150000" required>
                    </div>
                    
                    <div class="form-group">
                        <label>결제방법</label>
                        <select name="payment_method">
                            <option value="카드">카드</option>
                            <option value="현금">현금</option>
                            <option value="계좌이체">계좌이체</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>특이사항</label>
                    <textarea name="notes" rows="3" placeholder="특별 요청사항이나 메모를 입력하세요..."></textarea>
                </div>
                
                <button type="submit" class="btn-success">🎫 기표 발송</button>
            </form>
        </div>

        <!-- 빠른 액션 -->
        <div class="quick-actions">
            <form method="POST" style="margin: 0;">
                <input type="hidden" name="action" value="send_bulk">
                <label>대량 발송 건수:</label>
                <input type="number" name="bulk_count" value="5" min="1" max="20" style="margin: 5px 0;">
                <button type="submit" class="btn-warning">📦 대량 기표 발송</button>
            </form>
            
            <form method="POST" style="margin: 0;">
                <input type="hidden" name="action" value="send_emergency">
                <label>긴급 메시지:</label>
                <input type="text" name="emergency_message" value="시스템 점검 예정" style="margin: 5px 0;">
                <button type="submit" class="btn-danger">🚨 긴급 알림 발송</button>
            </form>
        </div>

        <!-- 에이전시 정보 -->
        <div class="agency-info">
            <h4>📊 오늘의 발송 현황</h4>
            <p><strong>총 발송:</strong> <span id="todayCount">0</span>건</p>
            <p><strong>CRM 서버:</strong> <span id="crmStatus">확인 중...</span></p>
            <p><strong>마지막 발송:</strong> <span id="lastSent">-</span></p>
        </div>

        <div style="text-align: center; margin-top: 30px; opacity: 0.8;">
            <p>🔗 CRM 시스템과 실시간 연동 중</p>
            <p>문의: abc-agency@example.com | Tel: 02-1234-5678</p>
        </div>
    </div>

    <script>
        // 페이지 로드 시 CRM 서버 상태 확인
        window.onload = function() {
            checkCRMStatus();
            updateTodayCount();
        };

        function checkCRMStatus() {
            fetch('notify.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('connectionStatus').textContent = '연결됨 ✅';
                document.getElementById('crmStatus').textContent = '정상 ✅';
            })
            .catch(error => {
                document.getElementById('connectionStatus').textContent = '연결 실패 ❌';
                document.getElementById('crmStatus').textContent = '연결 실패 ❌';
                console.error('CRM 서버 상태 확인 실패:', error);
            });
        }

        function updateTodayCount() {
            // 실제로는 서버에서 오늘의 발송 건수를 가져올 것
            const count = Math.floor(Math.random() * 50) + 10;
            document.getElementById('todayCount').textContent = count;
            
            const now = new Date();
            document.getElementById('lastSent').textContent = now.toLocaleTimeString();
        }

        // 폼 제출 후 카운트 업데이트
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                setTimeout(() => {
                    updateTodayCount();
                }, 1000);
            });
        });
    </script>
</body>
</html>