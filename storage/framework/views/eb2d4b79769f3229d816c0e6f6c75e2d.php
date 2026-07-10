<!-- resources/views/components/ai-assistant.blade.php -->
<style>
    /* AI Floating Button Styles */
    .ai-float-container {
        position: fixed;
        z-index: 99999 !important;
        user-select: none;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    
    .ai-main-btn {
        background: transparent !important;
        border-radius: 50%;
        display: flex !important;
        align-items: center;
        justify-content: center;
        box-shadow: none !important;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        border: none !important;
        outline: none !important;
        padding: 0;
    }
    
    .ai-main-btn:hover {
        transform: scale(1.05);
    }
    
    .ai-main-btn img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        display: block !important;
        pointer-events: auto;
    }
    
    .ai-drag-handle {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        cursor: grab;
        opacity: 0;
        background: transparent;
    }
    
    .ai-drag-handle:active { cursor: grabbing; }
    
    .ai-pulse { display: none !important; }
    
    .ai-chat-panel {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 480px;
        max-width: calc(100vw - 40px);
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(59, 130, 246, 0.3);
        z-index: 9999;
        display: none;
        flex-direction: column;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }
    
    .ai-chat-panel.show {
        display: flex;
        animation: aiSlideUp 0.3s ease;
    }
    
    @keyframes aiSlideUp {
        from { opacity: 0; transform: translate(-50%, -40%); }
        to { opacity: 1; transform: translate(-50%, -50%); }
    }
    
    .ai-chat-header {
        background: linear-gradient(135deg, #1e3a5f, #1e40af);
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: move;
    }
    
    .ai-chat-header h4 {
        color: white;
        font-weight: 600;
        margin: 0;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .ai-chat-header h4 img {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .ai-chat-header .chat-controls { display: flex; gap: 12px; }
    .ai-chat-header button {
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 14px;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .ai-chat-header button:hover { opacity: 1; }
    
    .ai-chat-messages {
        height: 450px;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .ai-message {
        display: flex;
        gap: 10px;
        animation: aiFadeIn 0.3s ease;
    }
    
    @keyframes aiFadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .ai-message.ai { justify-content: flex-start; }
    .ai-message.user { justify-content: flex-end; }
    
    .ai-message-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #1e3a5f;
    }
    
    .ai-message.ai .ai-message-avatar img {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .ai-message.user .ai-message-avatar {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .ai-message.user .ai-message-avatar i {
        font-size: 14px;
        color: white;
    }
    
    .ai-message-bubble {
        max-width: 75%;
        padding: 10px 14px;
        border-radius: 18px;
        font-size: 13px;
        line-height: 1.5;
    }
    
    .ai-message-bubble strong {
        color: #60a5fa;
        font-weight: 600;
    }
    
    .ai-message-bubble br {
        margin-bottom: 4px;
    }
    
    .ai-message-bubble ul, .ai-message-bubble ol {
        margin: 8px 0;
        padding-left: 20px;
    }
    
    .ai-message-bubble li {
        margin: 4px 0;
    }
    
    .ai-message.ai .ai-message-bubble {
        background: rgba(30, 58, 95, 0.3);
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: #e2e8f0;
        border-bottom-left-radius: 4px;
    }
    
    .ai-message.user .ai-message-bubble {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-bottom-right-radius: 4px;
    }
    
    .ai-chat-input {
        padding: 12px 16px;
        border-top: 1px solid rgba(59, 130, 246, 0.2);
        display: flex;
        gap: 10px;
    }
    
    .ai-chat-input input {
        flex: 1;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 25px;
        padding: 10px 15px;
        color: white;
        font-size: 12px;
        outline: none;
    }
    
    .ai-chat-input input:focus { border-color: #3b82f6; }
    .ai-chat-input input::placeholder { color: rgba(255, 255, 255, 0.4); }
    
    .ai-chat-input button {
        background: transparent;
        border: none;
        border-radius: 25px;
        padding: 0 18px;
        color: white;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }
    
    .ai-chat-input button:hover {
        transform: scale(1.02);
        background: linear-gradient(135deg, #1e40af, #1e3a5f);
    }
    
    .ai-typing {
        display: flex;
        gap: 4px;
        padding: 8px 12px;
    }
    
    .ai-typing span {
        width: 6px;
        height: 6px;
        background: #3b82f6;
        border-radius: 50%;
        animation: aiTyping 1.4s infinite;
    }
    
    .ai-typing span:nth-child(2) { animation-delay: 0.2s; }
    .ai-typing span:nth-child(3) { animation-delay: 0.4s; }
    
    @keyframes aiTyping {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
        30% { transform: translateY(-6px); opacity: 1; }
    }
    
    /* ✅ Cursor blinking for typewriter effect */
    .ai-cursor {
        display: inline-block;
        width: 2px;
        height: 16px;
        background: #60a5fa;
        margin-left: 2px;
        vertical-align: text-bottom;
        animation: aiCursorBlink 0.8s step-end infinite;
    }
    
    @keyframes aiCursorBlink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }
    
    .ai-icon-list {
        list-style: none;
        padding-left: 0;
        margin-top: 8px;
    }
    
    .ai-icon-list li {
        margin: 6px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .ai-icon-list li i {
        width: 16px;
        font-size: 11px;
        color: #60a5fa;
    }
    
    .ai-ticket-btn {
        display: inline-block;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        margin-top: 10px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .ai-ticket-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
    
    @media (max-width: 768px) {
        .ai-main-btn img { width: 95px !important; height: 95px !important; }
        .ai-chat-panel { width: 95% !important; }
        .ai-chat-messages { height: 380px !important; }
    }
    
    .ai-float-container.hide-button {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
</style>

<!-- AI Floating Button Container -->
<div id="aiFloatContainer" class="ai-float-container" style="bottom: 100px; right: 20px;">
    <div class="ai-main-btn" id="aiMainBtn">
        <div class="ai-pulse"></div>
        <img src="/images/void.png" alt="Void AI" id="aiLogoImage">
        <div class="ai-drag-handle" id="aiDragHandle"></div>
    </div>
</div>

<!-- AI Chat Panel -->
<div id="aiChatPanel" class="ai-chat-panel">
    <div class="ai-chat-header" id="aiChatHeader">
        <h4>
            <img src="/images/void.png" alt="Void AI" id="aiChatLogo">
            Void AI Assistant
        </h4>
        <div class="chat-controls">
            <button id="aiMinimizeBtn" title="Minimize"><i class="fas fa-minus"></i></button>
            <button id="aiCloseBtn" title="Close"><i class="fas fa-times"></i></button>
        </div>
    </div>
    
    <div class="ai-chat-messages" id="aiChatMessages">
        <div class="ai-message ai">
            <div class="ai-message-avatar">
                <img src="/images/void.png" alt="Void AI">
            </div>
            <div class="ai-message-bubble" id="aiWelcomeMessage">
                Loading...
            </div>
        </div>
    </div>
    
    <div class="ai-chat-input">
        <input type="text" id="aiChatInput" placeholder="Type your message... English, Tagalog, or Bisaya!">
        <button id="aiSendBtn"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script>
(function() {
    // ============ DOM ELEMENTS ============
    const floatContainer = document.getElementById('aiFloatContainer');
    const aiMainBtn = document.getElementById('aiMainBtn');
    const chatPanel = document.getElementById('aiChatPanel');
    const aiCloseBtn = document.getElementById('aiCloseBtn');
    const aiMinimizeBtn = document.getElementById('aiMinimizeBtn');
    const aiSendBtn = document.getElementById('aiSendBtn');
    const aiChatInput = document.getElementById('aiChatInput');
    const aiChatMessages = document.getElementById('aiChatMessages');
    
    // ============ USER DATA ============
    let currentUser = window.VoidUserData || null;
    
    // ============ FORMAT TEXT FUNCTION ============
    function formatText(text) {
        if (!text) return '';
        
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\n/g, '<br>');
        
        // Bullet points
        const bulletPattern = /(?:^|<br>)(?:\s*[•\-\*]\s+)([^<]+)/g;
        let hasBullets = text.match(/[•\-\*]\s+\S+/);
        
        if (hasBullets) {
            let lines = text.split('<br>');
            let inList = false;
            let processedLines = [];
            
            for (let i = 0; i < lines.length; i++) {
                let line = lines[i];
                let bulletMatch = line.match(/^\s*[•\-\*]\s+(.*)$/);
                
                if (bulletMatch) {
                    if (!inList) {
                        processedLines.push('<ul style="margin: 8px 0; padding-left: 20px;">');
                        inList = true;
                    }
                    processedLines.push(`<li style="margin: 4px 0;">${bulletMatch[1]}</li>`);
                } else {
                    if (inList) {
                        processedLines.push('</ul>');
                        inList = false;
                    }
                    if (line.trim()) {
                        processedLines.push(line);
                    }
                }
            }
            if (inList) {
                processedLines.push('</ul>');
            }
            text = processedLines.join('<br>');
        }
        
        // Numbered lists
        const numberPattern = /(?:^|<br>)(\d+)\.\s+([^<]+)/g;
        if (text.match(/\d+\.\s+\S+/)) {
            let lines = text.split('<br>');
            let inNumList = false;
            let processedLines = [];
            let lastNum = 0;
            
            for (let i = 0; i < lines.length; i++) {
                let line = lines[i];
                let numMatch = line.match(/^(\d+)\.\s+(.*)$/);
                
                if (numMatch) {
                    let currentNum = parseInt(numMatch[1]);
                    if (!inNumList || currentNum <= lastNum) {
                        if (inNumList) processedLines.push('</ol>');
                        processedLines.push('<ol style="margin: 8px 0; padding-left: 20px;">');
                        inNumList = true;
                    }
                    processedLines.push(`<li style="margin: 4px 0;">${numMatch[2]}</li>`);
                    lastNum = currentNum;
                } else {
                    if (inNumList) {
                        processedLines.push('</ol>');
                        inNumList = false;
                    }
                    if (line.trim()) {
                        processedLines.push(line);
                    }
                }
            }
            if (inNumList) {
                processedLines.push('</ol>');
            }
            text = processedLines.join('<br>');
        }
        
        text = text.replace(/(<br>){3,}/g, '<br><br>');
        
        return text;
    }
    
    // ============ DETECT LANGUAGE ============
    function detectLanguage(text) {
        const lowerText = text.toLowerCase();
        
        const tagalogMarkers = ['kumusta', 'musta', 'kamusta', 'salamat', 'paano', 'bakit', 'ano', 'sino', 'saan', 'kailan', 'magkano', 'pwede', 'gusto', 'meron', 'wala', 'oo', 'hindi', 'opo', 'nga', 'ito', 'iyan', 'yun', 'kasi', 'dahil', 'para', 'lang', 'naman', 'talaga', 'kaya', 'ngayon', 'alam', 'gusto', 'tulong'];
        const bisayaMarkers = ['musta', 'kamusta', 'salamat', 'paano', 'ngano', 'unsa', 'kinsa', 'asa', 'kanus-a', 'pila', 'pwede', 'gusto', 'naa', 'wala', 'oo', 'dili', 'ani', 'kana', 'adto', 'gikan', 'padulong', 'mao', 'dinhi', 'didto', 'karon', 'ugma', 'ganiha'];
        
        for (let word of tagalogMarkers) {
            if (lowerText.includes(word)) return 'tagalog';
        }
        for (let word of bisayaMarkers) {
            if (lowerText.includes(word)) return 'bisaya';
        }
        return 'english';
    }
    
    // ============ CALL VOID AI API ============
    async function callVoidAI(question, lang) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            let apiLang = 'en';
            if (lang === 'tagalog') apiLang = 'tl';
            if (lang === 'bisaya') apiLang = 'bisaya';
            
            const url = `/ai-ask?question=${encodeURIComponent(question)}&language=${apiLang}`;
            
            console.log('Calling Void AI:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                console.error('API error:', response.status);
                return null;
            }
            
            const data = await response.json();
            
            if (data.answer) {
                return data.answer;
            }
            
            return null;
        } catch (error) {
            console.error('API Error:', error);
            return null;
        }
    }
    
    // ============ TEACHING COMMAND (ONLY LOCAL) ============
    async function handleTeachingCommand(question) {
        const q = question.toLowerCase();
        
        if (q.startsWith('teach me:') || (q.includes(' - ') && q.length > 20)) {
            const parts = question.split('-');
            if (parts.length >= 2) {
                const newQ = parts[0].replace(/teach me:/i, '').trim();
                const newA = parts.slice(1).join('-').trim();
                if (newQ && newA) {
                    let learned = JSON.parse(localStorage.getItem('voidAI_learned') || '[]');
                    const existing = learned.find(l => l.q === newQ.toLowerCase());
                    if (!existing) {
                        learned.push({ q: newQ.toLowerCase(), a: newA, learnedAt: new Date().toISOString() });
                        localStorage.setItem('voidAI_learned', JSON.stringify(learned));
                        return `✅ <strong>I learned something new!</strong><br><br>📝 "${newQ}"<br>💡 "${newA}"<br><br>Thanks for teaching me! 🎓`;
                    }
                    return `📚 I already know that! The answer is: ${existing.a.substring(0, 100)}...`;
                }
            }
            return `📚 <strong>How to teach me:</strong><br><br>Format: "Teach me: [question] - [answer]"<br><br>Example: "Teach me: How to pay fees - Go to Accounting"`;
        }
        
        return null;
    }
    
    // ============ CREATE TICKET BUTTON ============
    function addTicketButton(response) {
        if (response.toLowerCase().includes('course or year change') || 
            response.toLowerCase().includes('change course') ||
            response.toLowerCase().includes('change year') ||
            response.toLowerCase().includes('support ticket')) {
            
            const ticketBtn = `<br><br><a href="/student/support-tickets" class="ai-ticket-btn" target="_blank">🎫 Create Support Ticket →</a>`;
            return response + ticketBtn;
        }
        return response;
    }
    
    // ============ ✅ TYPEWRITER EFFECT (NO FREEZE) ============
    function typeText(element, html, speed = 20) {
        return new Promise((resolve) => {
            // If text is short or empty, just display immediately
            if (!html || html.length < 20) {
                element.innerHTML = html;
                resolve();
                return;
            }
            
            // Get plain text for counting
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const fullText = tempDiv.textContent;
            const totalChars = fullText.length;
            
            // Speed based on text length
            const actualSpeed = totalChars < 50 ? 10 : (totalChars < 200 ? 18 : 28);
            
            // Start with empty element
            element.innerHTML = '';
            
            let charIndex = 0;
            let isActive = true;
            
            function revealNextChar() {
                if (!isActive) return;
                
                if (charIndex >= totalChars) {
                    // Done - show full HTML
                    element.innerHTML = html;
                    isActive = false;
                    resolve();
                    return;
                }
                
                // Build HTML with revealed text
                let result = '';
                let textPos = 0;
                let inTag = false;
                let tagBuffer = '';
                
                for (let i = 0; i < html.length; i++) {
                    const ch = html[i];
                    if (ch === '<') {
                        inTag = true;
                        tagBuffer = '<';
                    } else if (ch === '>') {
                        inTag = false;
                        tagBuffer += '>';
                        result += tagBuffer;
                        tagBuffer = '';
                    } else if (inTag) {
                        tagBuffer += ch;
                    } else {
                        // Text content - only reveal up to charIndex
                        if (textPos < charIndex) {
                            result += ch;
                        }
                        textPos++;
                    }
                }
                
                // Add cursor at the end
                element.innerHTML = result + '<span class="ai-cursor"></span>';
                
                charIndex++;
                
                // Continue typing with random delay for natural feel
                const delay = actualSpeed + (Math.random() * 15);
                setTimeout(revealNextChar, delay);
            }
            
            // Start after a small delay
            setTimeout(revealNextChar, 400);
        });
    }
    
    // ============ MAIN AI RESPONSE ============
    window.getAIResponse = async function(question) {
        const teachingResponse = await handleTeachingCommand(question);
        if (teachingResponse) {
            return formatText(teachingResponse);
        }
        
        const lang = detectLanguage(question);
        
        console.log('Question:', question);
        console.log('Language:', lang);
        
        // Show typing indicator
        showTyping();
        
        let answer = null;
        
        let apiLang = 'en';
        if (lang === 'tagalog') apiLang = 'tl';
        if (lang === 'bisaya') apiLang = 'bisaya';
        
        try {
            answer = await callVoidAI(question, apiLang);
            console.log('Void AI response received:', answer ? 'Yes' : 'No');
        } catch (error) {
            console.error('Void AI error:', error);
        }
        
        // Hide typing indicator
        hideTyping();
        
        if (!answer) {
            if (lang === 'tagalog') {
                answer = "Sorry, hindi ako maka-connect ngayon. Please check iyong internet connection at subukan ulit. 😊";
            } else if (lang === 'bisaya') {
                answer = "Sorry, dili ko maka-connect karon. Palihug check sa imong internet connection ug sulayi pag-usab. 😊";
            } else {
                answer = "Sorry, I'm having trouble connecting. Please check your internet connection and try again. 😊";
            }
        }
        
        let formattedAnswer = formatText(answer);
        formattedAnswer = addTicketButton(formattedAnswer);
        
        return formattedAnswer;
    };
    
    // ============ DARK MODE LOGO SWITCHER ============
    function updateAILogo() {
        const isDarkMode = document.body.classList.contains('dark');
        const logoImages = document.querySelectorAll('#aiLogoImage, #aiChatLogo, .ai-message-avatar img');
        logoImages.forEach(img => {
            if (isDarkMode) {
                if (img.src && !img.src.includes('voiddark.png')) img.src = '/images/voiddark.png';
            } else {
                if (img.src && !img.src.includes('void.png')) img.src = '/images/void.png';
            }
        });
    }
    
    setTimeout(updateAILogo, 100);
    
    const observer = new MutationObserver(() => updateAILogo());
    observer.observe(document.body, { attributes: true });
    
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) darkModeToggle.addEventListener('click', () => setTimeout(updateAILogo, 50));
    
    // ============ DRAG & DROP ============
    let isDragging = false;
    let dragStartX = 0, dragStartY = 0;
    let hasMoved = false;
    let touchTimer = null;
    
    const savedLeft = localStorage.getItem('aiFloatLeft');
    const savedTop = localStorage.getItem('aiFloatTop');
    if (savedLeft && savedTop) {
        floatContainer.style.left = savedLeft;
        floatContainer.style.top = savedTop;
        floatContainer.style.right = 'auto';
        floatContainer.style.bottom = 'auto';
    } else {
        floatContainer.style.bottom = '100px';
        floatContainer.style.right = '20px';
        floatContainer.style.left = 'auto';
        floatContainer.style.top = 'auto';
    }
    
    function onDragStart(e) {
        if (touchTimer) clearTimeout(touchTimer);
        dragStartX = e.clientX || (e.touches && e.touches[0].clientX);
        dragStartY = e.clientY || (e.touches && e.touches[0].clientY);
        hasMoved = false;
        isDragging = true;
        
        document.addEventListener('mousemove', onDragMove);
        document.addEventListener('mouseup', onDragEnd);
        document.addEventListener('touchmove', onDragMove, { passive: false });
        document.addEventListener('touchend', onDragEnd);
        if (e.cancelable) e.preventDefault();
    }
    
    function onDragMove(e) {
        if (!isDragging) return;
        const currentX = e.clientX || (e.touches && e.touches[0].clientX);
        const currentY = e.clientY || (e.touches && e.touches[0].clientY);
        const deltaX = Math.abs(currentX - dragStartX);
        const deltaY = Math.abs(currentY - dragStartY);
        
        if (deltaX > 8 || deltaY > 8) {
            hasMoved = true;
            let currentLeft = parseFloat(floatContainer.style.left);
            let currentTop = parseFloat(floatContainer.style.top);
            if (isNaN(currentLeft)) currentLeft = window.innerWidth - 90;
            if (isNaN(currentTop)) currentTop = window.innerHeight - 180;
            
            let newLeft = currentLeft + (currentX - dragStartX);
            let newTop = currentTop + (currentY - dragStartY);
            newLeft = Math.min(window.innerWidth - 85, Math.max(5, newLeft));
            newTop = Math.min(window.innerHeight - 150, Math.max(60, newTop));
            
            floatContainer.style.left = newLeft + 'px';
            floatContainer.style.top = newTop + 'px';
            floatContainer.style.right = 'auto';
            floatContainer.style.bottom = 'auto';
            dragStartX = currentX;
            dragStartY = currentY;
        }
        if (e.cancelable) e.preventDefault();
    }
    
    function onDragEnd(e) {
        isDragging = false;
        if (hasMoved) {
            localStorage.setItem('aiFloatLeft', floatContainer.style.left);
            localStorage.setItem('aiFloatTop', floatContainer.style.top);
        }
        document.removeEventListener('mousemove', onDragMove);
        document.removeEventListener('mouseup', onDragEnd);
        document.removeEventListener('touchmove', onDragMove);
        document.removeEventListener('touchend', onDragEnd);
        touchTimer = setTimeout(() => { hasMoved = false; }, 100);
    }
    
    function hideButton() { floatContainer.classList.add('hide-button'); }
    function showButton() { floatContainer.classList.remove('hide-button'); }
    
    function onClick(e) {
        if (hasMoved) {
            e.preventDefault();
            e.stopPropagation();
            return;
        }
        
        if (chatPanel.classList.contains('show')) {
            chatPanel.classList.remove('show');
            localStorage.setItem('aiPanelOpen', 'closed');
            showButton();
        } else {
            chatPanel.classList.add('show');
            localStorage.setItem('aiPanelOpen', 'open');
            hideButton();
            aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
        }
    }
    
    if (aiMainBtn) {
        aiMainBtn.addEventListener('mousedown', onDragStart);
        aiMainBtn.addEventListener('click', onClick);
        aiMainBtn.addEventListener('touchstart', onDragStart, { passive: false });
        aiMainBtn.addEventListener('touchend', function(e) {
            setTimeout(() => {
                if (!hasMoved) {
                    e.preventDefault();
                    onClick(e);
                }
            }, 50);
        });
    }
    
    if (localStorage.getItem('aiPanelOpen') === 'open') {
        chatPanel.classList.add('show');
        hideButton();
    } else {
        showButton();
    }
    
    if (aiCloseBtn) aiCloseBtn.addEventListener('click', () => {
        chatPanel.classList.remove('show');
        localStorage.setItem('aiPanelOpen', 'closed');
        showButton();
    });
    
    if (aiMinimizeBtn) aiMinimizeBtn.addEventListener('click', () => {
        chatPanel.classList.remove('show');
        localStorage.setItem('aiPanelOpen', 'closed');
        showButton();
    });
    
    // ============ ✅ FIXED: MESSAGE FUNCTIONS WITH TYPEWRITER ============
    let typingElement = null;
    
    function addMessage(message, isUser = false) {
        const div = document.createElement('div');
        div.className = `ai-message ${isUser ? 'user' : 'ai'}`;
        
        if (isUser) {
            div.innerHTML = `<div class="ai-message-avatar"><i class="fas fa-user"></i></div><div class="ai-message-bubble">${escapeHtml(message)}</div>`;
            aiChatMessages.appendChild(div);
            aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
        } else {
            div.innerHTML = `<div class="ai-message-avatar"><img src="/images/void.png" alt="Void AI"></div><div class="ai-message-bubble" id="aiResponseBubble"></div>`;
            aiChatMessages.appendChild(div);
            aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
            
            // ✅ Typewriter effect for AI response
            const bubble = div.querySelector('#aiResponseBubble');
            if (bubble) {
                typeText(bubble, message, 15);
            }
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function showTyping() {
        hideTyping();
        
        const div = document.createElement('div');
        div.className = 'ai-message ai';
        div.id = 'aiTyping';
        div.innerHTML = `<div class="ai-message-avatar"><img src="/images/void.png" alt="Void AI"></div><div class="ai-message-bubble"><div class="ai-typing"><span></span><span></span><span></span></div></div>`;
        aiChatMessages.appendChild(div);
        aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
        typingElement = div;
    }
    
    function hideTyping() {
        const typing = document.getElementById('aiTyping');
        if (typing) {
            typing.remove();
            typingElement = null;
        }
    }
    
    // ✅ SEND MESSAGE WITH TYPEWRITER
    async function sendMessage() {
        const msg = aiChatInput.value.trim();
        if (!msg) return;
        
        aiChatInput.disabled = true;
        aiSendBtn.disabled = true;
        
        addMessage(msg, true);
        aiChatInput.value = '';
        
        showTyping();
        
        try {
            const response = await window.getAIResponse(msg);
            hideTyping();
            addMessage(response, false);
        } catch (error) {
            console.error('Error:', error);
            hideTyping();
            const errorMsg = 'Sorry, something went wrong. Please try again. 😊';
            addMessage(formatText(errorMsg), false);
        } finally {
            aiChatInput.disabled = false;
            aiSendBtn.disabled = false;
            aiChatInput.focus();
        }
    }
    
    if (aiSendBtn) aiSendBtn.addEventListener('click', sendMessage);
    if (aiChatInput) {
        aiChatInput.addEventListener('keypress', (e) => { 
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
    
    // Welcome message with typewriter
    setTimeout(() => {
        const welcome = document.getElementById('aiWelcomeMessage');
        if (welcome) {
            let msg = '';
            if (currentUser) {
                msg = formatText(`👋 <strong>Hi ${currentUser.name}!</strong>

I'm <strong>Void</strong>, your AI clearance assistant.

Ask me anything about clearance! 😊`);
            } else {
                msg = formatText(`👋 <strong>Hello!</strong>

I'm <strong>Void</strong>, your AI clearance assistant.

Ask me anything about clearance! 😊`);
            }
            // Apply typewriter effect to welcome message
            typeText(welcome, msg, 20);
        }
    }, 500);
    
    // Image fallback
    document.querySelectorAll('img[src="/images/void.png"], img[src="/images/voiddark.png"]').forEach(img => {
        img.onerror = function() {
            this.style.display = 'none';
            if (this.parentElement.classList.contains('ai-message-avatar')) {
                this.parentElement.innerHTML = '<i class="fas fa-robot text-white text-sm"></i>';
            } else if (this.parentElement.classList.contains('ai-main-btn')) {
                this.parentElement.innerHTML = '<i class="fas fa-robot text-white text-2xl"></i>';
            }
        };
    });
    
    console.log('✅ Void AI Assistant Loaded with Typewriter Effect');
})();
</script><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/components/ai-assistant.blade.php ENDPATH**/ ?>