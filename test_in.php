<?php
// test_in.php - 내부 CRM 시스템
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏢 CRM 관리 시스템</title>
    <style>
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            margin: 0; 
            background: #f5f5f5;
            color: #333;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .container { 
            max-width: 1200px; 
            margin: 20px auto; 
            padding: 0 20px;
        }
        .dashboard {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .widget {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .widget h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        .widget .number {
            font-size: 2em;
            font-weight: bold;
            color: #3498db;
        }
        .main-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        .ticket-area {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .ticket-header {
            background: #34495e;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        .connection-status {
            padding: 15px 20px;
            background: #ecf0f1;
            border-bottom: 1px solid #ddd;
        }
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .status-connected { background: #27ae60; }
        .status-disconnected { background: #e74c3c; }
        .status-connecting { background: #f39c12; animation: pulse 1s infinite; }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .ticket-list {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
        }
        .ticket-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 10px 0;
            padding: 15px;
            background: #fff;
            transition: all 0.3s;
            border-left: 4px solid #3498db;
        }
        .ticket-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .ticket-item.priority-긴급 {
            border-left-color: #e74c3c;
            background: #fdf2f2;
        }
        .ticket-item.priority-VIP {
            border-left-color: #f39c12;
            background: #fef9e7;
        }
        .ticket-item.emergency {
            border-left-color: #e74c3c;
            background: #fdf2f2;
            border: 2px solid #e74c3c;
        }
        .ticket-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .ticket-id {
            font-weight: bold;
            color: #2c3e50;
        }
        .ticket-time {
            font-size: 0.9em;
            color: #7f8c8d;
        }
        .ticket-priority {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .priority-일반 { background: #d5f4e6; color: #27ae60; }
        .priority-긴급 { background: #fadbd8; color: #e74c3c; }
        .priority-VIP { background: #fdeaa7; color: #f39c12; }
        .emergency-tag { background: #e74c3c; color: white; }
        
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .control-panel {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        button { 
            padding: 10px 15px; 
            margin: 5px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-primary { background: #3498db; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-danger { background: #e74c3c; color: white; }
        
        button:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        
        .stats-panel {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .alert-sound {
            display: none;
        }
        
        .user-info {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #3498db;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏢 CRM 관리 시스템</h1>
        <p>실시간 기표 접수 및 관리 | 담당자: 김관리</p>
    </div>

    <div class="container">
        <!-- 대시보드 위젯 -->
        <div class="dashboard">
            <div class="widget">
                <h3>📊 오늘 접수</h3>
                <div class="number" id="todayTickets">0</div>
                <p>건</p>
            </div>
            <div class="widget">
                <h3>⚡ 긴급 처리</h3>
                <div class="number" id="urgentTickets">0</div>
                <p>건</p>
            </div>
            <div class="widget">
                <h3>💰 총 금액</h3>
                <div class="number" id="totalAmount">0</div>
                <p>원</p>
            </div>
        </div>

        <div class="main-content">
            <!-- 메인 기표 영역 -->
            <div class="ticket-area">
                <div class="ticket-header">
                    <h3>🎫 실시간 기표 접수</h3>
                    <button onclick="clearTickets()" class="btn-warning">목록 지우기</button>
                </div>
                
                <div class="connection-status">
                    <span class="status-indicator status-connecting" id="statusIndicator"></span>
                    <span id="connectionText">서버 연결 중...</span>
                    <span style="float: right;">
                        <button onclick="connect()" class="btn-primary">연결</button>
                        <button onclick="disconnect()" class="btn-warning">연결 끊기</button>
                    </span>
                </div>
                
                <div class="ticket-list" id="ticketList">
                    <div style="text-align: center; color: #7f8c8d; margin-top: 50px;">
                        <h4>📭 접수된 기표가 없습니다</h4>
                        <p>외부 에이전시에서 기표를 보내면 여기에 실시간으로 표시됩니다.</p>
                    </div>
                </div>
            </div>

            <!-- 사이드바 -->
            <div class="sidebar">
                <!-- 사용자 정보 -->
                <div class="user-info">
                    <div class="user-avatar">김</div>
                    <h4>김관리 팀장</h4>
                    <p>접수 담당자</p>
                    <p><strong>접속 시간:</strong> <?= date('H:i:s') ?></p>
                </div>

                <!-- 제어판 -->
                <div class="control-panel">
                    <h4>🎛️ 제어판</h4>
                    <button onclick="testConnection()" class="btn-primary">연결 테스트</button>
                    <button onclick="playTestAlert()" class="btn-success">알림음 테스트</button>
                    <button onclick="refreshStats()" class="btn-warning">통계 새로고침</button>
                    
                    <hr style="margin: 15px 0;">
                    
                    <h5>🔔 알림 설정</h5>
                    <label>
                        <input type="checkbox" id="soundAlert" checked> 사운드 알림
                    </label><br>
                    <label>
                        <input type="checkbox" id="browserAlert" checked> 브라우저 알림
                    </label><br>
                    <label>
                        <input type="checkbox" id="emergencyOnly"> 긴급만 알림
                    </label>
                </div>

                <!-- 통계 패널 -->
                <div class="stats-panel">
                    <h4>📈 실시간 통계</h4>
                    <p><strong>연결된 사용자:</strong> <span id="connectedUsers">1</span>명</p>
                    <p><strong>평균 응답시간:</strong> <span id="avgResponseTime">< 1</span>초</p>
                    <p><strong>서버 가동시간:</strong> <span id="uptime">00:00:00</span></p>
                    
                    <hr style="margin: 15px 0;">
                    
                    <h5>📊 에이전시별 현황</h5>
                    <div id="agencyStats">
                        <p>ABC 여행사: <strong>0</strong>건</p>
                        <p>XYZ 투어: <strong>0</strong>건</p>
                        <p>기타: <strong>0</strong>건</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 알림음 -->
    <audio id="alertSound" class="alert-sound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmMbBT2X2/PCdSMELIbP8daNN" type="audio/wav">
    </audio>

    <script>
        let ws = null;
        let isConnected = false;
        let ticketCount = 0;
        let urgentCount = 0;
        let totalAmount = 0;
        let agencyStats = {};

        // WebSocket 연결
        function connect() {
            if (ws && ws.readyState === WebSocket.OPEN) {
                updateConnectionStatus('이미 연결되어 있습니다.', 'connected');
                return;
            }

            updateConnectionStatus('연결 중...', 'connecting');

            try {
                ws = new WebSocket('ws://localhost:8080');
                
                ws.onopen = function() {
                    isConnected = true;
                    updateConnectionStatus('실시간 연결됨', 'connected');
                    addSystemMessage('CRM 시스템에 연결되었습니다.');
                };

                ws.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    console.log('받은 데이터:', data);
                    
                    if (data.type === 'new_ticket') {
                        addTicketToList(data);
                        updateStats(data);
                        playAlert(data);
                        showBrowserNotification(data.message);
                    } else if (data.type === 'emergency_alert') {
                        addEmergencyAlert(data);
                        playEmergencyAlert();
                        showBrowserNotification('🚨 ' + data.message);
                    } else if (data.type === 'connected') {
                        addSystemMessage(data.message);
                    } else if (data.type === 'custom') {
                        addCustomMessage(data);
                    }
                };

                ws.onclose = function() {
                    isConnected = false;
                    updateConnectionStatus('연결 끊김', 'disconnected');
                    addSystemMessage('서버와의 연결이 끊어졌습니다.');
                };

                ws.onerror = function(error) {
                    updateConnectionStatus('연결 오류', 'disconnected');
                    addSystemMessage('연결 오류: ' + error);
                };

            } catch (error) {
                updateConnectionStatus('연결 실패', 'disconnected');
                addSystemMessage('연결 실패: ' + error.message);
            }
        }

        function disconnect() {
            if (ws) {
                ws.close();
                ws = null;
            }
        }

        function updateConnectionStatus(message, status) {
            const indicator = document.getElementById('statusIndicator');
            const text = document.getElementById('connectionText');
            
            text.textContent = message;
            indicator.className = 'status-indicator status-' + status;
        }

        // 기표를 목록에 추가
        function addTicketToList(data) {
            const ticketList = document.getElementById('ticketList');
            
            // 첫 번째 기표일 때 안내 메시지 제거
            if (ticketCount === 0) {
                ticketList.innerHTML = '';
            }
            
            const ticketItem = document.createElement('div');
            const ticketData = data.data;
            const priority = ticketData.priority || '일반';
            
            ticketItem.className = `ticket-item priority-${priority}`;
            ticketItem.innerHTML = `
                <div class="ticket-meta">
                    <span class="ticket-id">${ticketData.ticket_id}</span>
                    <span class="ticket-priority priority-${priority}">${priority}</span>
                    <span class="ticket-time">${new Date().toLocaleTimeString()}</span>
                </div>
                <div style="margin: 10px 0;">
                    <strong>${data.message}</strong>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.9em;">
                    <div><strong>고객:</strong> ${ticketData.customer_name}</div>
                    <div><strong>에이전시:</strong> ${ticketData.agency}</div>
                    <div><strong>출발:</strong> ${ticketData.departure || '-'}</div>
                    <div><strong>도착:</strong> ${ticketData.destination || '-'}</div>
                    <div><strong>금액:</strong> ${formatNumber(ticketData.amount)}원</div>
                    <div><strong>결제:</strong> ${ticketData.payment_method || '-'}</div>
                </div>
                ${ticketData.notes ? `<div style="margin-top: 10px; padding: 8px; background: #f8f9fa; border-radius: 4px; font-size: 0.9em;"><strong>메모:</strong> ${ticketData.notes}</div>` : ''}
            `;
            
            // 최신 기표를 맨 위에 추가
            ticketList.insertBefore(ticketItem, ticketList.firstChild);
            
            // 애니메이션 효과
            ticketItem.style.opacity = '0';
            ticketItem.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                ticketItem.style.opacity = '1';
                ticketItem.style.transform = 'translateY(0)';
                ticketItem.style.transition = 'all 0.3s ease';
            }, 100);
        }

        // 긴급 알림 추가
        function addEmergencyAlert(data) {
            const ticketList = document.getElementById('ticketList');
            
            if (ticketCount === 0) {
                ticketList.innerHTML = '';
            }
            
            const alertItem = document.createElement('div');
            alertItem.className = 'ticket-item emergency';
            alertItem.innerHTML = `
                <div class="ticket-meta">
                    <span class="ticket-id">${data.data.alert_id}</span>
                    <span class="ticket-priority emergency-tag">긴급 알림</span>
                    <span class="ticket-time">${new Date().toLocaleTimeString()}</span>
                </div>
                <div style="margin: 10px 0;">
                    <strong>${data.message}</strong>
                </div>
                <div style="font-size: 0.9em;">
                    <strong>발송 에이전시:</strong> ${data.data.agency}<br>
                    <strong>알림 레벨:</strong> ${data.data.alert_level}
                </div>
            `;
            
            ticketList.insertBefore(alertItem, ticketList.firstChild);
            
            // 긴급 알림 깜빡임 효과
            let blinkCount = 0;
            const blinkInterval = setInterval(() => {
                alertItem.style.backgroundColor = blinkCount % 2 === 0 ? '#ffebee' : '#fdf2f2';
                blinkCount++;
                if (blinkCount >= 6) {
                    clearInterval(blinkInterval);
                }
            }, 300);
        }

        // 시스템 메시지 추가
        function addSystemMessage(message) {
            const ticketList = document.getElementById('ticketList');
            
            const systemItem = document.createElement('div');
            systemItem.style.cssText = 'text-align: center; padding: 10px; color: #7f8c8d; font-style: italic; border-bottom: 1px solid #ecf0f1;';
            systemItem.innerHTML = `<small>${new Date().toLocaleTimeString()} - ${message}</small>`;
            
            ticketList.insertBefore(systemItem, ticketList.firstChild);
            
            // 5초 후 제거
            setTimeout(() => {
                if (systemItem.parentNode) {
                    systemItem.parentNode.removeChild(systemItem);
                }
            }, 5000);
        }

        // 커스텀 메시지 추가
        function addCustomMessage(data) {
            const ticketList = document.getElementById('ticketList');
            
            const customItem = document.createElement('div');
            customItem.className = 'ticket-item';
            customItem.style.borderLeftColor = '#9b59b6';
            customItem.innerHTML = `
                <div class="ticket-meta">
                    <span class="ticket-id">CUSTOM_${Date.now()}</span>
                    <span class="ticket-priority" style="background: #e8daef; color: #9b59b6;">사용자 정의</span>
                    <span class="ticket-time">${new Date().toLocaleTimeString()}</span>
                </div>
                <div style="margin: 10px 0;">
                    <strong>${data.message}</strong>
                </div>
                <div style="font-size: 0.9em;">
                    <strong>발송자:</strong> ${data.data.sent_from || '알 수 없음'}
                </div>
            `;
            
            ticketList.insertBefore(customItem, ticketList.firstChild);
        }

        // 통계 업데이트
        function updateStats(data) {
            const ticketData = data.data;
            
            ticketCount++;
            if (ticketData.priority === '긴급') {
                urgentCount++;
            }
            totalAmount += parseInt(ticketData.amount || 0);
            
            // 에이전시별 통계
            const agency = ticketData.agency || '기타';
            agencyStats[agency] = (agencyStats[agency] || 0) + 1;
            
            // 화면 업데이트
            document.getElementById('todayTickets').textContent = ticketCount;
            document.getElementById('urgentTickets').textContent = urgentCount;
            document.getElementById('totalAmount').textContent = formatNumber(totalAmount);
            
            updateAgencyStats();
        }

        function updateAgencyStats() {
            const agencyStatsEl = document.getElementById('agencyStats');
            let html = '';
            for (const [agency, count] of Object.entries(agencyStats)) {
                html += `<p>${agency}: <strong>${count}</strong>건</p>`;
            }
            agencyStatsEl.innerHTML = html || '<p>데이터 없음</p>';
        }

        // 알림 재생
        function playAlert(data) {
            const soundEnabled = document.getElementById('soundAlert').checked;
            const emergencyOnly = document.getElementById('emergencyOnly').checked;
            
            if (!soundEnabled) return;
            if (emergencyOnly && data.data.priority !== '긴급') return;
            
            try {
                const audio = document.getElementById('alertSound');
                audio.currentTime = 0;
                audio.play().catch(e => console.log('사운드 재생 실패:', e));
            } catch (e) {
                console.log('사운드 재생 오류:', e);
            }
        }

        function playEmergencyAlert() {
            // 긴급 알림용 특별한 사운드 (여러 번 재생)
            for (let i = 0; i < 3; i++) {
                setTimeout(() => playAlert({data: {priority: '긴급'}}), i * 500);
            }
        }

        function playTestAlert() {
            playAlert({data: {priority: '긴급'}});
            addSystemMessage('테스트 알림음이 재생되었습니다.');
        }

        // 브라우저 알림
        function showBrowserNotification(message) {
            const browserEnabled = document.getElementById('browserAlert').checked;
            
            if (!browserEnabled) return;
            
            if (Notification.permission === "granted") {
                new Notification("CRM 알림", {
                    body: message,
                    icon: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E🎫%3C/text%3E%3C/svg%3E"
                });
            }
        }

        // 유틸리티 함수들
        function formatNumber(num) {
            return new Intl.NumberFormat('ko-KR').format(num);
        }

        function clearTickets() {
            if (confirm('모든 기표 목록을 지우시겠습니까?')) {
                document.getElementById('ticketList').innerHTML = `
                    <div style="text-align: center; color: #7f8c8d; margin-top: 50px;">
                        <h4>📭 접수된 기표가 없습니다</h4>
                        <p>외부 에이전시에서 기표를 보내면 여기에 실시간으로 표시됩니다.</p>
                    </div>
                `;
                
                // 통계 초기화
                ticketCount = 0;
                urgentCount = 0;
                totalAmount = 0;
                agencyStats = {};
                
                document.getElementById('todayTickets').textContent = '0';
                document.getElementById('urgentTickets').textContent = '0';
                document.getElementById('totalAmount').textContent = '0';
                updateAgencyStats();
                
                addSystemMessage('기표 목록이 초기화되었습니다.');
            }
        }

        function testConnection() {
            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify({type: 'ping'}));
                addSystemMessage('연결 테스트 신호를 보냈습니다.');
            } else {
                addSystemMessage('WebSocket 연결이 필요합니다.');
            }
        }

        function refreshStats() {
            updateAgencyStats();
            addSystemMessage('통계가 새로고침되었습니다.');
        }

        // 페이지 로드시 자동 실행
        window.onload = function() {
            // 브라우저 알림 권한 요청
            if (Notification.permission === "default") {
                Notification.requestPermission();
            }
            
            // 1초 후 자동 연결
            setTimeout(() => {
                connect();
            }, 1000);
            
            // 3초마다 ping 전송하여 연결 유지
            setInterval(() => {
                if (ws && ws.readyState === WebSocket.OPEN) {
                    ws.send(JSON.stringify({type: 'ping'}));
                }
            }, 3000);
            
            // 5초마다 알림 체크
            setInterval(() => {
                if (ws && ws.readyState === WebSocket.OPEN) {
                    ws.send(JSON.stringify({type: 'check_notifications'}));
                }
            }, 5000);
            
            // 가동시간 표시
            let startTime = Date.now();
            setInterval(() => {
                const uptime = Date.now() - startTime;
                const hours = Math.floor(uptime / 3600000);
                const minutes = Math.floor((uptime % 3600000) / 60000);
                const seconds = Math.floor((uptime % 60000) / 1000);
                document.getElementById('uptime').textContent = 
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        };
    </script>
</body>
</html>