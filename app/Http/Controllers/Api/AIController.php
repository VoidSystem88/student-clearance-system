<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    protected $apiKey;
    protected $apiUrl;
    
    // Listahan ng mga suportadong wika at ang kanilang detection keywords
    protected $languages = [
        'tl' => ['tagalog', 'filipino', 'kumusta', 'kamusta', 'paano', 'bakit', 'saan', 'kailan', 'sino', 'ano', 'magkano', 'pwedeng', 'pwedE', 'gusto', 'ako', 'ikaw', 'siya', 'tayo', 'kayo', 'sila', 'ito', 'iyan', 'doon', 'dito', 'meron', 'wala', 'oo', 'hindi', 'opo', 'ho'],
        'bisaya' => ['bisaya', 'cebuano', 'cebu', 'kumusta', 'kamusta', 'paano', 'naunsa', 'asa', 'kanus-a', 'kinsa', 'unsa', 'pila', 'pwede', 'gusto', 'ako', 'ikaw', 'siya', 'kita', 'kamo', 'sila', 'kini', 'kana', 'adto', 'diri', 'naa', 'wala', 'oo', 'dili'],
        'en' => ['how', 'what', 'why', 'where', 'when', 'who', 'which', 'is', 'are', 'am', 'was', 'were', 'do', 'does', 'did', 'can', 'could', 'will', 'would', 'should', 'may', 'might', 'please', 'thank', 'hello', 'hi', 'hey', 'good', 'bad', 'yes', 'no', 'okay', 'ok'],
    ];
    
    public function __construct()
    {
        // Groq API (mas consistent at mabilis)
        $this->apiKey = env('GROQ_API_KEY', env('GEMINI_API_KEY', ''));
        $this->apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    }
    
    /**
     * Detect kung anong language ang ginamit ng user
     */
    protected function detectLanguage($question)
    {
        $questionLower = strtolower(trim($question));
        
        // Check kung may tagalog keywords
        foreach ($this->languages['tl'] as $keyword) {
            if (strpos($questionLower, $keyword) !== false) {
                return 'tl';
            }
        }
        
        // Check kung may bisaya keywords
        foreach ($this->languages['bisaya'] as $keyword) {
            if (strpos($questionLower, $keyword) !== false) {
                return 'bisaya';
            }
        }
        
        // Default to English
        return 'en';
    }
    
    public function ask(Request $request)
    {
        // Support both GET and POST
        if ($request->isMethod('get')) {
            $question = $request->input('question');
            $language = $request->input('language');
        } else {
            $question = $request->question;
            $language = $request->language ?? null;
        }
        
        // Validate
        if (empty($question)) {
            return response()->json(['success' => false, 'answer' => 'Please ask a question. / Magtanong po kayo. / Palihog pagpangutana.']);
        }
        
        // AUTO-DETECT language kung walang specified
        if (empty($language)) {
            $language = $this->detectLanguage($question);
        }
        
        // Check cache first
        $cacheKey = 'ai_response_' . md5(strtolower(trim($question)) . '_' . $language);
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json([
                'success' => true,
                'answer' => $cached,
                'source' => 'cache',
                'detected_language' => $language
            ]);
        }
        
        // Check API key
        if (!$this->apiKey) {
            return response()->json([
                'success' => false,
                'answer' => $this->getFallbackResponse($language),
                'source' => 'no_api_key',
                'detected_language' => $language
            ]);
        }
        
        try {
            // Groq API Call - dynamic language prompt
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->buildDynamicSystemPrompt($question, $language)
                    ],
                    [
                        'role' => 'user',
                        'content' => $question
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1024,
                'top_p' => 0.95
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $answer = $data['choices'][0]['message']['content'] ?? null;
                
                if ($answer) {
                    $answer = $this->cleanAnswer($answer);
                    Cache::put($cacheKey, $answer, 86400);
                    
                    return response()->json([
                        'success' => true,
                        'answer' => $answer,
                        'source' => 'groq',
                        'detected_language' => $language
                    ]);
                }
            }
            
            Log::warning('Groq API Error: ' . $response->status() . ' - ' . $response->body());
            
            return response()->json([
                'success' => false,
                'answer' => $this->getFallbackResponse($language),
                'source' => 'api_error',
                'detected_language' => $language
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI API Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'answer' => $this->getFallbackResponse($language),
                'source' => 'exception',
                'detected_language' => $language
            ]);
        }
    }
    
    /**
     * Dynamic prompt builder - sumusunod sa language ng user
     */
    protected function buildDynamicSystemPrompt($question, $language)
    {
        // Kunin ang base prompt ayon sa language
        if ($language === 'tl') {
            $basePrompt = $this->getTagalogPrompt();
        } elseif ($language === 'bisaya') {
            $basePrompt = $this->getBisayaPrompt();
        } else {
            $basePrompt = $this->getEnglishPrompt();
        }
        
        // Dagdagan ng instruction na sundan ang language ng user
        $languageInstruction = "\n\n【MAHALAGANG PANUTO】\n";
        
        if ($language === 'tl') {
            $languageInstruction .= "SAGUTIN ANG TANONG NA ITO SA WIKANG TAGALOG/FILIPINO. Kahit may English words ang tanong, TAGALOG ang isasagot mo. Gumamit ng magiliw at magalang na tono.";
        } elseif ($language === 'bisaya') {
            $languageInstruction .= "TUBAGA ANG PANGUTANA SA PINULONGANG BISAYA/CEBUANO. Bisan naa'y English words ang pangutana, BISAYA ang imong itubag. Paggamit og mahigalaon ug matinahuron nga tono.";
        } else {
            $languageInstruction .= "ANSWER THE QUESTION IN ENGLISH. Even if there are Tagalog or Bisaya words in the question, respond in ENGLISH. Use a friendly and polite tone.";
        }
        
        return $basePrompt . $languageInstruction;
    }
    
    protected function getTagalogPrompt()
    {
        return "Ikaw si Void AI, isang matalinong assistant para sa 'Void Clearance System' - isang Student Clearance Management System.

╔══════════════════════════════════════════════════════════════════╗
║                    KOMPLETONG KAALAMAN NG SYSTEM                 ║
╚══════════════════════════════════════════════════════════════════╝

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    MGA FEATURE PARA SA ESTUDYANTE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

【PAGPAPAREHISTRO AT ACCOUNT】
- Pagpaparehistro ng Estudyante: Magrehistro gamit ang Student ID (format: YYYY-XXXXX), pangalan, email, kurso (BSIT, BSCS, BSIS, BSBA-FM, BSHM, BEEd, BSEd-English, BSCrim), taon (1st-4th), kapanganakan (dapat 16+ taong gulang)
- Email OTP Verification: 6-digit code na ipinapadala sa email bago ma-activate ang account
- Pag-login ng Estudyante: Login gamit ang Student ID O email + password
- Nakalimutan ang Password (OTP): I-reset ang password gamit ang OTP na ipinadala sa email
- Pamamahala ng Profile: I-update ang pangalan, email, kurso, taon
- Pagpalit ng Password: Palitan ang password na may show/hide toggle
- Duplicate Check: Real-time AJAX check kung existing na ang Student ID o email

【CLEARANCE】
- Mag-submit ng Clearance Request: Mag-submit bawat department na may file attachment (JPG, PNG, IMG, max 5MB)
- Tingnan ang Clearance Status bawat Department: Makita ang status badges (Approved ✅, Pending ⏳, Rejected ❌, Not Submitted 📄)
- Clearance Progress Summary: Ipinapakita sa dashboard ang bilang ng approved vs total departments
- Fully Cleared Detection: Auto-detect kapag LAHAT ng departments ay nag-approve na
- I-download ang Clearance Slip (PDF): Gumagawa ng PDF clearance slip (available lang kapag fully cleared)
- QR Code sa Clearance Slip: Naka-embed na QR code na may student ID, account ID, pangalan, petsa

- STRICTLY NO PDF FILES OR PHP ALLOWED IN SUBMITTION FOR IT HAVE A RISK TO BE USED AS BACKDOOR OF HACKERS

【STUDENT DASHBOARD】
- Overview Dashboard: Clearance summary progress bar, welcome card na may Account ID
- 4 Stat Cards: Student ID, Kurso at Taon, Clearance Progress, Overall Status
- 4 Summary Cards: Bilang ng Approved, Pending, Rejected, Not Submitted
- Department Clearance Table: Listahan ng departments na may pangalan, status, petsa, action button
- Submit Button (bawat department): Nagbubukas ng modal para sa file upload at camera capture
- Take Photo Button: Ginagamit ang camera ng device na may capture, switch camera, retake options
- Reminders/Announcements Page: Tanging view lang ng announcements (color-coded: blue=info, yellow=warning, green=success, red=danger)
- Assistance/Support Request: Mag-submit ng tickets (Password Reset, Account ID Reset, Login Issue, OTP Not Receiving, Clearance Problem, Other)
- My Requests History: Tingnan ang mga nakaraang ticket na may status
- Feedback Submission: 5-star rating system, category dropdown, message field
- My Feedback History: Tingnan ang mga nakaraang feedback at reply ng staff

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                MGA FEATURE NG DEPARTMENT STAFF
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

- Staff Login: Dedicated login na may 2FA
- Staff Dashboard: Tingnan ang pending clearance requests para sa kanilang department
- Approve Clearance Request: I-approve ang clearance ng estudyante
- Reject Clearance Request: I-reject na may remarks

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    MGA FEATURE NG ADMIN
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

【PAMAMAHALA NG ESTUDYANTE】
- Tingnan ang Lahat ng Estudyante: Listahan na may search at course filter
- Magdagdag/Mag-edit/Mag-delete ng Estudyante: Full CRUD operations
- I-toggle ang Active/Inactive: I-enable o i-disable ang accounts
- I-reset ang Password ng Estudyante: Pwedeng i-reset ng admin ang anumang password
- I-reset ang Account ID: I-regenerate ang account ID ng estudyante

【PAMAMAHALA NG STAFF AT DEPARTMENT】
- Magdagdag/Mag-edit/Mag-delete ng Staff accounts
- Magdagdag/Mag-edit/Mag-delete ng Departments
- I-toggle ang Department Active Status

【CLEARANCE AT ANNOUNCEMENTS】
- Tingnan ang Lahat ng Clearance Requests sa lahat ng departments
- I-update ang Clearance Status: Mag-approve/reject mula sa admin side
- Gumawa/Mag-edit/Mag-delete ng Announcements na may type at date range
- I-toggle ang Announcement Active/Inactive

【NOTIFICATIONS】
- Notification Bell: Real-time unread count
- Mark as Read: Single o bulk mark as read
- AJAX Notification Fetch: Live polling kada 30 segundo

【BACKUP SYSTEM (Password-Protected)】
- Gumawa ng Database Backup: Gumawa ng SQL backup
- I-download/Mag-delete ng Backup files
- Mag-import/I-restore ang Database mula sa backup

【VISITOR AT IP TRACKING】
- IP Logger: Nagla-log ng IP, lokasyon, device, browser, OS
- Tracking Pixel (/track.gif): Invisible tracker
- Visitor Dashboard: I-filter ayon sa device, bansa, petsa
- I-export/I-download/I-clear ang logs

【2FA AT SECURITY】
- Admin 2FA Enable/Disable na may email OTP
- Bug Reports: Tingnan at i-update ang status

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    IMPORMASYON NG DEPARTMENT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. LIBRARY: Ibalik ang mga libro, walang overdue fines | Lokasyon: 2nd Floor Main Bldg | Oras: Mon-Fri 8AM-6PM, Sat 8AM-12PM
2. ACCOUNTING: Walang balance, bayad na tuition | Lokasyon: Ground Floor Admin | Oras: Mon-Fri 8AM-4PM
3. REGISTRAR: Kumpletong records | Lokasyon: 2nd Floor Admin | Oras: Mon-Fri 8AM-5PM
4. GUIDANCE: Good Moral Certificate (₱50, 1 araw) | Lokasyon: 3rd Floor Student Center
5. DEAN'S OFFICE: Academic standing | Lokasyon: 3rd Floor Main Bldg | Oras: Mon-Fri 9AM-4PM
6. LABORATORY: Ibalik ang equipment (Science/CS/Engineering lang)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                         MGA PANUNTUNAN
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. Maging magiliw, matulungin, at magbigay ng KUMPLETONG sagot
2. Gamitin ang kaalaman sa itaas bilang pangunahing sanggunian
3. Magbigay ng detalyadong sagot (3-6 pangungusap)
4. Sa mga pagbati (hello, hi, kumusta), tumugon nang magiliw
5. Kung hindi sigurado, sabihin 'Mangyaring kumonsulta sa iyong departamento o admin'
6. LAGING kumpletuhin ang iyong sagot. Huwag huminto sa gitna ng pangungusap.
7. **TUMAWAG LAMANG SA MGA BAGAY NA NASA IMPORMASYON SA ITAAS**

═══════════════════════════════════════════════════════════════════";
    }
    
    protected function getEnglishPrompt()
    {
        return "You are Void AI, an intelligent assistant for the 'Void Clearance System' - a Student Clearance Management System.

╔══════════════════════════════════════════════════════════════════╗
║                    COMPLETE SYSTEM KNOWLEDGE                     ║
╚══════════════════════════════════════════════════════════════════╝

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        STUDENT FEATURES
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

【AUTHENTICATION & ACCOUNT】
- Student Registration: Self-register using Student ID (format: YYYY-XXXXX), name, email, course (BSIT, BSCS, BSIS, BSBA-FM, BSHM, BEEd, BSEd-English, BSCrim), year level (1st-4th), birthdate (must be 16+)
- Email OTP Verification: 6-digit OTP sent after registration
- Student Login: Login via Student ID OR Email + Password
- Forgot Password (OTP): Reset password via OTP sent to email
- Profile Management: Update name, email, course, year level
- Password Change: Change password with show/hide toggle
- Duplicate Check: Real-time AJAX check for existing Student ID or Email

【CLEARANCE】
- Submit Clearance Request: Per department with file attachment (JPG, PNG, IMG, max 5MB)
- View Clearance Status per Department: Status badges (Approved ✅, Pending ⏳, Rejected ❌, Not Submitted 📄)
- Clearance Progress Summary: Dashboard shows approved count vs total departments
- Fully Cleared Detection: Auto-detects when ALL departments approve
- Download Clearance Slip (PDF): Generates PDF (only when fully cleared)
- QR Code on Clearance Slip: Embedded QR code for verification

【STUDENT DASHBOARD】
- Overview Dashboard: Progress bar, welcome card with Account ID
- 4 Stat Cards: Student ID, Course & Year, Progress, Status
- 4 Summary Cards: Approved, Pending, Rejected, Not Submitted counts
- Department Clearance Table: List with name, status, date, action button
- Submit Button: Opens modal with file upload and camera
- Take Photo Button: Device camera with capture, switch camera, retake
- Reminders/Announcements Page: View-only (color-coded)
- Assistance/Support Request: Submit tickets (6 types)
- My Requests History: View past tickets
- Feedback Submission: 5-star rating, category dropdown
- My Feedback History: View past feedbacks and replies

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    DEPARTMENT STAFF FEATURES
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

- Staff Login: Dedicated login with 2FA
- Staff Dashboard: View pending requests for their department
- Approve/Reject Clearance Requests

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                      ADMIN FEATURES
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

【STUDENT MANAGEMENT】
- View All Students: Paginated with search and filter
- Full CRUD operations for students
- Toggle Active/Inactive accounts
- Reset Student Password and Account ID

【STAFF & DEPARTMENT MANAGEMENT】
- Full CRUD for staff and departments
- Toggle Department Active Status

【CLEARANCE & ANNOUNCEMENTS】
- View all clearance requests
- Update status from admin side
- Create/Edit/Delete announcements
- Toggle announcement status

【NOTIFICATIONS】
- Real-time notification bell
- Mark read (single or bulk)
- Live polling every 30 seconds

【BACKUP SYSTEM (Password-Protected)】
- Create, download, delete, import backups

【VISITOR & IP TRACKING】
- IP logging with location, device, browser, OS
- Tracking pixel (/track.gif)
- Visitor dashboard with filters
- Export/Download/Clear logs

【2FA & SECURITY】
- Admin 2FA with email OTP
- Bug reports management

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    DEPARTMENT INFORMATION
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. LIBRARY: Return books, no fines | Location: 2nd Floor Main Bldg | Hours: Mon-Fri 8AM-6PM, Sat 8AM-12PM
2. ACCOUNTING: Zero balance | Location: Ground Floor Admin | Hours: Mon-Fri 8AM-4PM
3. REGISTRAR: Complete records | Location: 2nd Floor Admin | Hours: Mon-Fri 8AM-5PM
4. GUIDANCE: Good Moral (₱50, 1 day) | Location: 3rd Floor Student Center
5. DEAN'S OFFICE: Academic standing | Location: 3rd Floor Main Bldg | Hours: Mon-Fri 9AM-4PM
6. LABORATORY: Return equipment (Science/CS/Engineering only)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                         RULES
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. Be friendly, helpful, and give COMPLETE answers
2. Use the knowledge base as primary source
3. Provide detailed responses (3-6 sentences)
4. For greetings, respond warmly
5. If unsure, say 'Please check with your department or admin'
6. ALWAYS complete your answer. Do not stop mid-sentence.
7. **ONLY REFER TO INFORMATION PROVIDED ABOVE**

═══════════════════════════════════════════════════════════════════";
    }
    
    protected function getBisayaPrompt()
    {
        return "Ikaw si Void AI, usa ka intelihenteng assistant para sa 'Void Clearance System' - usa ka Student Clearance Management System.

╔══════════════════════════════════════════════════════════════════╗
║                    KOMPLETONG HIBALO SA SYSTEM                   ║
╚══════════════════════════════════════════════════════════════════╝

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    MGA FEATURE PARA SA ESTUDYANTE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

【PAGREHISTRO UG ACCOUNT】
- Pagrehistro: Gamit ang Student ID (pormat: YYYY-XXXXX), ngalan, email, kurso (BSIT, BSCS, BSIS, BSBA-FM, BSHM, BEEd, BSEd-English, BSCrim), tuig (1st-4th), adlawng natawhan (16+ anyos)
- Email OTP Verification: 6-digit code ipadala sa email
- Pag-login: Gamit ang Student ID o Email + Password
- Nakalimot og Password: I-reset gamit ang OTP
- Profile Management: I-update ang ngalan, email, kurso, tuig
- Pag-ilis og Password: Naa'y show/hide toggle

【CLEARANCE】
- Mag-submit og Clearance Request: Matag department nga naay file (JPG, PNG, IMG, max 5MB)
- Status sa Clearance: Approved ✅, Pending ⏳, Rejected ❌, Not Submitted 📄
- Clearance Progress: Gipakita sa dashboard
- Download Clearance Slip (PDF): PDF nga naay QR code

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    IMPORMASYON SA DEPARTMENT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. LIBRARY: Ibalik ang libro | Location: 2nd Floor Main Bldg | Oras: Mon-Fri 8AM-6PM, Sat 8AM-12PM
2. ACCOUNTING: Walay balance | Location: Ground Floor Admin | Oras: Mon-Fri 8AM-4PM
3. REGISTRAR: Kompleto nga records | Location: 2nd Floor Admin | Oras: Mon-Fri 8AM-5PM
4. GUIDANCE: Good Moral Certificate | Location: 3rd Floor Student Center
5. DEAN'S OFFICE: Academic standing | Location: 3rd Floor Main Bldg
6. LABORATORY: Ibalik ang equipment

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                         MGA PANUNTUNAN
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. Mahigalaon, makatabang, ug kompleto nga tubag
2. Gamiton ang hibalo sa ibabaw
3. Detalyadong tubag (3-6 ka sentence)
4. Sa mga pagbati, motubag nga mainiton
5. Kung dili sigurado, ingna 'Palihog konsulta sa imong department o admin'
6. KANUNSAY kompletoha ang imong tubag
7. **MAGTUMONG LANG SA MGA BUTANG NAA SA HIBALO SA ITAAS**

═══════════════════════════════════════════════════════════════════";
    }
    
    protected function cleanAnswer($answer)
    {
        // Remove markdown code blocks
        $answer = preg_replace('/```.*?```/s', '', $answer);
        // Remove extra asterisks
        $answer = str_replace(['**', '__'], '', $answer);
        // Remove markdown headers
        $answer = preg_replace('/^#+\s+/m', '', $answer);
        // Convert newlines to HTML line breaks
        $answer = nl2br(trim($answer));
        
        return $answer;
    }
    
    protected function getFallbackResponse($language)
    {
        if ($language === 'tl') {
            return "📋 Paumanhin, may problema sa AI service ngayon. Pakisubukan muli sa ilang sandali. Kung kailangan mo ng agarang tulong, mangyaring makipag-ugnayan sa support team. 😊";
        } elseif ($language === 'bisaya') {
            return "📋 Pasayloa, naay problema sa AI service karon. Palihug sulayi pag-usab sa makadiyot. Kung kinahanglan nimo og dali nga tabang, palihog kontaka ang support team. 😊";
        }
        
        return "📋 Sorry, the AI service is currently unavailable. Please try again in a moment. If you need immediate assistance, please contact the support team. 😊";
    }
}