// resources/js/ai-responses.js
// Dedicated AI Response System for Void Clearance Assistant

const VoidAI = {
    // Version
    version: '1.0.0',
    
    // Knowledge Base - Dito natin lalagyan ng data
    knowledge: {
        // Document Submission
        submission: {
            keywords: ['submit', 'upload', 'mag submit', 'pano magsubmit', 'paano magsubmit', 'how to submit', 'send document', 'mag pasa', 'ipasa', 'mag upload', 'paano mag upload'],
            responses: {
                en: "📄 HOW TO SUBMIT DOCUMENTS:\n\n1. Go to 'Clearance' page from sidebar\n2. Click each department section\n3. Click 'Upload Document' button\n4. Select JPG or PNG image only\n5. Click 'Submit for Review'\n\n⚠️ Only images (JPEG/JPG/PNG) are allowed for security.",
                tl: "📄 PAANO MAG-SUBMIT NG DOCUMENTS:\n\n1. Pumunta sa 'Clearance' page sa sidebar\n2. I-click ang bawat department section\n3. I-click ang 'Upload Document' button\n4. Pumili ng JPG o PNG image lamang\n5. I-click ang 'Submit for Review'\n\n⚠️ Mga imahe lang (JPEG/JPG/PNG) ang pinapayagan para sa seguridad.",
                bisaya: "📄 UNSAON PAG-SUBMIT OG DOCUMENTS:\n\n1. Adto sa 'Clearance' page sa sidebar\n2. I-click ang kada department section\n3. I-click ang 'Upload Document' button\n4. Pilia ang JPG o PNG image lang\n5. I-click ang 'Submit for Review'\n\n⚠️ Mga imahe lang (JPEG/JPG/PNG) ang gitugotan para sa seguridad."
            }
        },
        
        // Requirements
        requirements: {
            keywords: ['requirement', 'kailangan', 'needed', 'gikinahanglan', 'requirements', 'ano kailangan', 'lista', 'ano ang kailangan'],
            responses: {
                en: "📋 CLEARANCE REQUIREMENTS:\n\n• Complete Student Information Form\n• Clearance Application Form\n• Valid Student ID\n• No outstanding balance (Library, Accounting)\n• Good Moral Certificate\n• Exit Interview Slip (for graduating students)\n\n⚠️ Submit as JPG/PNG images only.",
                tl: "📋 MGA KAILANGAN SA CLEARANCE:\n\n• Complete Student Information Form\n• Clearance Application Form\n• Valid Student ID\n• Walang outstanding balance (Library, Accounting)\n• Good Moral Certificate\n• Exit Interview Slip (kung graduating)\n\n⚠️ I-submit bilang JPG/PNG images lamang.",
                bisaya: "📋 MGA GIKINAHANGLAN SA CLEARANCE:\n\n• Complete Student Information Form\n• Clearance Application Form\n• Valid Student ID\n• Walay outstanding balance (Library, Accounting)\n• Good Moral Certificate\n• Exit Interview Slip (kung graduating)\n\n⚠️ I-submit isip JPG/PNG images lamang."
            }
        },
        
        // Status Check
        status: {
            keywords: ['status', 'progress', 'update', 'balita', 'clearance status', 'ano status', 'result', 'kamusta clearance ko'],
            responses: {
                en: "📊 YOUR CLEARANCE STATUS:\n\nCheck your Dashboard to see:\n• Overall progress bar\n• Completed departments\n• Pending requirements\n\nVisit the Clearance page for detailed per-department status.\n\nTip: Green means cleared, Yellow means pending!",
                tl: "📊 STATUS NG CLEARANCE MO:\n\nTingnan sa Dashboard para makita:\n• Overall progress bar\n• Completed departments\n• Pending requirements\n\nPumunta sa Clearance page para sa detailed per-department status.\n\nTip: Berde ay cleared, Dilaw ay pending!",
                bisaya: "📊 STATUS SA IMONG CLEARANCE:\n\nTan-awa sa Dashboard aron makita:\n• Overall progress bar\n• Completed departments\n• Pending requirements\n\nAdto sa Clearance page para sa detailed per-department status.\n\nTip: Berde kay cleared, Dalag kay pending!"
            }
        },
        
        // Processing Time
        processing: {
            keywords: ['how long', 'gaano katagal', 'pila ka adlaw', 'days', 'weeks', 'processing time', 'ilang araw', 'kailan matatapos'],
            responses: {
                en: "⏰ PROCESSING TIMELINE:\n\n• Document review: 2-3 business days\n• Department approvals: 1-2 days per department\n• Full clearance: Typically 1-2 weeks\n\nYou'll receive email notifications for each update.",
                tl: "⏰ ORAS NG PAGPROSESO:\n\n• Review ng documents: 2-3 business days\n• Approval per department: 1-2 days bawat department\n• Full clearance: Karaniwang 1-2 linggo\n\nMakakatanggap ka ng email notification sa bawat update.",
                bisaya: "⏰ ORAS SA PAGPROSESO:\n\n• Review sa documents: 2-3 business days\n• Approval per department: 1-2 days kada department\n• Full clearance: Kasagarang 1-2 ka semana\n\nMakadawat ka og email notification sa matag update."
            }
        },
        
        // Password Reset
        password: {
            keywords: ['forgot', 'password', 'nakalimutan', 'reset', 'change password', 'kalimot', 'limot'],
            responses: {
                en: "🔐 PASSWORD RESET:\n\n1. Click 'Forgot Password' on login page\n2. Enter your Student ID or Email\n3. Check your email for OTP code\n4. Enter the 6-digit OTP\n5. Create new password\n\nNeed help? Visit 'Request Assistance' page.",
                tl: "🔐 PAG-RESET NG PASSWORD:\n\n1. I-click ang 'Forgot Password' sa login page\n2. Ilagay ang Student ID o Email\n3. Tingnan ang email para sa OTP code\n4. Ipasok ang 6-digit OTP\n5. Gumawa ng bagong password\n\nKailangan ng tulong? Pumunta sa 'Request Assistance' page.",
                bisaya: "🔐 PAG-RESET SA PASSWORD:\n\n1. I-click ang 'Forgot Password' sa login page\n2. Ibutang ang Student ID o Email\n3. Tan-awa ang email para sa OTP code\n4. Ibutang ang 6-digit OTP\n5. Paghimo og bag-ong password\n\nNagkinahanglan og tabang? Adto sa 'Request Assistance' page."
            }
        },
        
        // Account Info
        account: {
            keywords: ['account', 'account id', 'my id', 'student id', 'where to find account', 'ano account ko'],
            responses: {
                en: "👤 ACCOUNT INFORMATION:\n\nYour Account ID is displayed on your Dashboard (top right card).\n\nYou can also view your complete profile in the 'My Profile' page.\n\nNeed to update info? Go to Profile and click Edit.",
                tl: "👤 IMPORMASYON NG ACCOUNT:\n\nAng Account ID mo ay makikita sa iyong Dashboard (top right card).\n\nMaaari mo ring tingnan ang iyong buong profile sa 'My Profile' page.\n\nKailangan mag-update ng info? Pumunta sa Profile at i-click ang Edit.",
                bisaya: "👤 IMPORMASYON SA ACCOUNT:\n\nAng Account ID nimo makita sa imong Dashboard (top right card).\n\nMahimo usab nimo tan-awon ang imong tibuok profile sa 'My Profile' page.\n\nNagkinahanglan mag-update og info? Adto sa Profile ug i-click ang Edit."
            }
        },
        
        // Creator / About
        creator: {
            keywords: ['sino gumawa', 'who created', 'who made', 'your creator', 'creador', 'developer', 'who is your master', 'sino nagawa', 'void system', 'anonymous owner'],
            responses: {
                en: "🤖 ABOUT MY CREATOR:\n\nI was created by VOID SYSTEM under the direction of the ANONYMOUS OWNER.\n\nMy purpose is to assist students with their clearance needs efficiently and securely.\n\nI'm designed to make the clearance process faster and easier for everyone!",
                tl: "🤖 TUNGKOL SA AKING LUMIKHA:\n\nAko ay ginawa ng VOID SYSTEM sa ilalim ng ANONYMOUS OWNER.\n\nAng layunin ko ay tumulong sa mga estudyante sa kanilang clearance nang mabilis at ligtas.\n\nAko ay dinisenyo para gawing mas mabilis at madali ang clearance process para sa lahat!",
                bisaya: "🤖 MAHITUNGOD SA AKONG NAGHIMO:\n\nGihimo ako sa VOID SYSTEM ubos sa ANONYMOUS OWNER.\n\nAng akong tumong kay mutabang sa mga estudyante sa ilang clearance nga paspas ug luwas.\n\nGidisenyo ako aron himuon nga mas paspas ug sayon ang clearance process para sa tanan!"
            }
        },
        
        // File Security
        security: {
            keywords: ['file', 'upload file', 'pdf', 'document', 'anong file', 'allowed file', 'what files', 'image only', 'jpg', 'png', 'jpeg'],
            responses: {
                en: "🔒 FILE SECURITY NOTICE:\n\nOnly JPEG, JPG, and PNG image files are allowed for upload.\n\n❌ PDF files are NOT accepted\n❌ Word documents are NOT accepted\n❌ ZIP files are NOT accepted\n\nThis is to prevent security risks and backdoor attempts.\n\nPlease convert your documents to clear JPG/PNG images before uploading.",
                tl: "🔒 PAALALA SA SEGURIDAD NG FILE:\n\nJPEG, JPG, at PNG image files lamang ang pinapayagan para i-upload.\n\n❌ Hindi tinatanggap ang PDF files\n❌ Hindi tinatanggap ang Word documents\n❌ Hindi tinatanggap ang ZIP files\n\nIto ay para maiwasan ang security risks at backdoor attempts.\n\nPakiconvert ang iyong mga dokumento sa malinaw na JPG/PNG images bago i-upload.",
                bisaya: "🔒 PAHIBALO SA SEGURIDAD SA FILE:\n\nJPEG, JPG, ug PNG image files ra ang gitugotan para i-upload.\n\n❌ Dili dawaton ang PDF files\n❌ Dili dawaton ang Word documents\n❌ Dili dawaton ang ZIP files\n\nKini aron malikayan ang security risks ug backdoor attempts.\n\nPalihog convert ang imong mga dokumento sa klaro nga JPG/PNG images sa dili pa i-upload."
            }
        },
        
        // Greetings
        greeting: {
            keywords: ['hello', 'hi', 'hey', 'kumusta', 'musta', 'kamusta', 'halo', 'helo', 'good morning', 'good afternoon', 'good evening', 'magandang umaga', 'magandang hapon', 'magandang gabi'],
            responses: {
                en: "Hello! 👋 I'm Void, your AI clearance assistant. How can I help you today?",
                tl: "Kumusta! 👋 Ako si Void, ang AI clearance assistant mo. Paano kita matutulungan ngayong araw?",
                bisaya: "Kumusta! 👋 Ako si Void, imong AI clearance assistant. Unsa akong matabang nimo karon?"
            }
        },
        
        // Jokes
        jokes: {
            keywords: ['joke', 'humor', 'tawa', 'nakakatawa', 'patawa', 'make me laugh', 'funny', 'comedy', 'biruan'],
            responses: [
                "Bakit laging mabagal ang clearance? Eh kasi ang bagal ng pila sa accounting! Hahaha! 😂",
                "What do you call a clearance that tells jokes? A pun-ishment! Get it? Pun? 🤪",
                "Why did the student bring a ladder to clearance? Because the requirements were 'high'! Badum tss! 🥁",
                "Bakit umiiyak ang estudyante? 'Clearance pending' pa rin kahit tapos na ang lahat! 😭",
                "Anong sabi ng registrar sa estudyante? 'Clearance muna bago alis, bes!' 😅",
                "Ngano dugay ang clearance? Kay naay department nga murag 'Oo pero hindi' vibes! Paita! 😅"
            ]
        },
        
        // Thanks
        thanks: {
            keywords: ['thank', 'salamat', 'thanks', 'thank you', 'salamuch', 'salamat po', 'ty', 'tnx'],
            responses: {
                en: "You're welcome! 😊 Happy to help. Come back if you need anything else. Good luck with your clearance! 🍀",
                tl: "Walang anuman! 😊 Masaya akong makatulong. Balik ka lang kung may kailangan ka pa. Good luck sa clearance mo! 🍀",
                bisaya: "Way sapayan! 😊 Malipayon ko nga nakatabang. Balik lang kung naa pa kay pangutana. Good luck sa imong clearance! 🍀"
            }
        }
    },
    
    // Learning memory (saves to localStorage)
    learnedResponses: [],
    
    // Initialize - load learned responses
    init: function() {
        const saved = localStorage.getItem('voidAI_learned');
        if (saved) {
            this.learnedResponses = JSON.parse(saved);
        }
        console.log('Void AI v' + this.version + ' ready!');
    },
    
    // Learn new response
    learn: function(question, answer) {
        this.learnedResponses.push({
            q: question.toLowerCase().trim(),
            a: answer,
            timestamp: new Date().toISOString(),
            used: 0
        });
        localStorage.setItem('voidAI_learned', JSON.stringify(this.learnedResponses));
        return true;
    },
    
    // Detect language
    detectLanguage: function(text) {
        const tagalogMarkers = ['kumusta', 'musta', 'kamusta', 'salamat', 'paano', 'bakit', 'ano', 'sino', 'saan', 'kailan', 'magkano', 'pwede', 'gusto', 'meron', 'wala', 'oo', 'hindi', 'opo', 'nga', 'ito', 'iyan', 'yun', 'dito', 'doon'];
        const bisayaMarkers = ['musta', 'kamusta', 'salamat', 'paano', 'ngano', 'unsa', 'kinsa', 'asa', 'kanus-a', 'pila', 'pwede', 'gusto', 'naa', 'wala', 'oo', 'dili', 'ani', 'kana', 'adto', 'pila', 'tagpila', 'gikan', 'padulong'];
        
        const lowerText = text.toLowerCase();
        
        // Check Bisaya
        let bisayaCount = 0;
        for (let word of bisayaMarkers) {
            if (lowerText.includes(word)) bisayaCount++;
        }
        if (bisayaCount >= 2) return 'bisaya';
        
        // Check Tagalog
        let tagalogCount = 0;
        for (let word of tagalogMarkers) {
            if (lowerText.includes(word)) tagalogCount++;
        }
        if (tagalogCount >= 1) return 'tagalog';
        
        return 'en';
    },
    
    // Spell correction for common typos
    correctSpelling: function(text) {
        let corrected = text.toLowerCase();
        const corrections = {
            'halo': 'hello', 'helo': 'hello', 'hllo': 'hello', 'heloo': 'hello',
            'kumsta': 'kumusta', 'kamsta': 'kumusta', 'musta na': 'kumusta',
            'slmat': 'salamat', 'slamat': 'salamat', 'salamt': 'salamat',
            'pno': 'paano', 'panu': 'paano', 'paanu': 'paano',
            'bkt': 'bakit', 'bket': 'bakit',
            'snu': 'sino', 'cinu': 'sino',
            'sn': 'saan', 'san': 'saan',
            'klan': 'kailan', 'kelan': 'kailan'
        };
        for (let [wrong, correct] of Object.entries(corrections)) {
            if (corrected.includes(wrong)) {
                corrected = corrected.replace(new RegExp(wrong, 'g'), correct);
            }
        }
        return corrected;
    },
    
    // Get response based on question
    getResponse: function(question) {
        // Apply spell correction
        const corrected = this.correctSpelling(question);
        const q = corrected.toLowerCase();
        const lang = this.detectLanguage(question);
        
        // Check learned responses first
        for (let learned of this.learnedResponses) {
            if (q.includes(learned.q) || learned.q.includes(q)) {
                learned.used++;
                localStorage.setItem('voidAI_learned', JSON.stringify(this.learnedResponses));
                return learned.a;
            }
        }
        
        // Check each knowledge category
        for (let [category, data] of Object.entries(this.knowledge)) {
            // Skip if no keywords
            if (!data.keywords) continue;
            
            // Check if question matches any keyword
            for (let keyword of data.keywords) {
                if (q.includes(keyword)) {
                    // For jokes, return random joke
                    if (category === 'jokes') {
                        return data.responses[Math.floor(Math.random() * data.responses.length)];
                    }
                    // For other categories, return response in detected language
                    if (data.responses && data.responses[lang]) {
                        return data.responses[lang];
                    }
                    if (data.responses && data.responses.en) {
                        return data.responses.en;
                    }
                }
            }
        }
        
        // No match found - return fallback response
        const fallbacks = [
            "Hmm, I'm not sure about that yet! 🤔 Try checking the 'Clearance' page or ask the admin. I'm still learning! 📚",
            "Good question! 👀 You might find the answer on your Dashboard. Want me to help with something else? 😊",
            "I'm still learning new things every day! 📖 For now, try the 'Request Assistance' page. I'll get smarter over time! 👍",
            "Interesting question! 🤓 Could you rephrase that? I want to make sure I understand correctly so I can help you better! 😄"
        ];
        
        // Add Tagalog/Bisaya fallbacks based on detected language
        if (lang === 'tagalog') {
            return "Hmm, hindi ko pa masyadong alam yan! 🙈 Subukan mo sa 'Clearance' page o 'Request Assistance'. Natututo pa ako! 📚";
        } else if (lang === 'bisaya') {
            return "Hmm, wala pa ko kahibalo ana! 🙈 Sulayi sa 'Clearance' page o 'Request Assistance'. Nagakat-on pa ko! 📚";
        }
        
        return fallbacks[Math.floor(Math.random() * fallbacks.length)];
    },
    
    // Get random greeting
    getGreeting: function() {
        const greetings = [
            "Hello! 👋 I'm Void, your AI clearance assistant.",
            "Hey there! 👋 Void here, ready to help with your clearance!",
            "Hi! 👋 Void AI at your service. How can I help today?",
            "Greetings! 👋 This is Void AI. Ask me anything about clearance!"
        ];
        return greetings[Math.floor(Math.random() * greetings.length)];
    }
};

// Initialize on load
VoidAI.init();

// Export for use (if needed)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VoidAI;
}