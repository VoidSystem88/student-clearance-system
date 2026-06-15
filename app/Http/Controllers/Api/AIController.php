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
    protected $promptVersion = 'v3';

    protected $languages = [
        'tl' => ['tagalog', 'filipino', 'kumusta', 'kamusta', 'paano', 'bakit', 'saan', 'kailan', 'sino', 'ano', 'magkano', 'pwedeng', 'pwedE', 'gusto', 'ako', 'ikaw', 'siya', 'tayo', 'kayo', 'sila', 'ito', 'iyan', 'doon', 'dito', 'meron', 'wala', 'oo', 'hindi', 'opo', 'ho'],
        'bisaya' => ['bisaya', 'cebuano', 'cebu', 'kumusta', 'kamusta', 'paano', 'naunsa', 'asa', 'kanus-a', 'kinsa', 'unsa', 'pila', 'pwede', 'gusto', 'ako', 'ikaw', 'siya', 'kita', 'kamo', 'sila', 'kini', 'kana', 'adto', 'diri', 'naa', 'wala', 'oo', 'dili'],
        'en' => ['how', 'what', 'why', 'where', 'when', 'who', 'which', 'is', 'are', 'am', 'was', 'were', 'do', 'does', 'did', 'can', 'could', 'will', 'would', 'should', 'may', 'might', 'please', 'thank', 'hello', 'hi', 'hey', 'good', 'bad', 'yes', 'no', 'okay', 'ok'],
    ];

    protected $availableCourses = ['BSIT', 'BSCS', 'BSIS', 'BSBA-FM', 'BSHM', 'BEEd', 'BSEd-English', 'BSCrim'];
    protected $availableYearLevels = ['1st', '2nd', '3rd', '4th'];

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY', env('GEMINI_API_KEY', ''));
        $this->apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    }

    protected function detectLanguage($question)
    {
        $questionLower = strtolower(trim($question));

        $bisayaExclusive = ['naunsa', 'asa', 'kanus-a', 'kinsa', 'unsa', 'pila', 'kita', 'kamo',
                            'kini', 'kana', 'adto', 'diri', 'naa', 'dili', 'bisaya', 'cebuano'];

        foreach ($bisayaExclusive as $keyword) {
            if (strpos($questionLower, $keyword) !== false) {
                return 'bisaya';
            }
        }

        $tagalogExclusive = ['tagalog', 'filipino', 'bakit', 'saan', 'kailan', 'sino', 'magkano',
                             'tayo', 'kayo', 'iyan', 'doon', 'dito', 'meron', 'hindi', 'opo', 'ho'];

        foreach ($tagalogExclusive as $keyword) {
            if (strpos($questionLower, $keyword) !== false) {
                return 'tl';
            }
        }

        return 'en';
    }

    protected function detectAssistanceType($question)
    {
        $questionLower = strtolower(trim($question));
        
        // Course change patterns
        $coursePatterns = [
            'change course', 'change my course', 'change program', 'shift course', 'shift program',
            'transfer course', 'different course', 'new course', 'another course',
            'palit kurso', 'change ng course', 'shift ng course', 'ibang course',
            'ilisdan kurso', 'usbon kurso', 'balhin kurso'
        ];
        
        // Year change patterns
        $yearPatterns = [
            'change year', 'change year level', 'change my year', 'increment year', 'advance year',
            'next year level', 'promotion', 'new year level', 'increase year',
            'palit taon', 'change ng year', 'ibang year level',
            'ilisdan tuig', 'usbon tuig', 'balhin tuig', 'increase year level'
        ];
        
        foreach ($coursePatterns as $pattern) {
            if (strpos($questionLower, $pattern) !== false) {
                return 'course_change';
            }
        }
        
        foreach ($yearPatterns as $pattern) {
            if (strpos($questionLower, $pattern) !== false) {
                return 'year_change';
            }
        }
        
        if ((strpos($questionLower, 'course') !== false || strpos($questionLower, 'program') !== false) && 
            (strpos($questionLower, 'change') !== false || strpos($questionLower, 'shift') !== false || strpos($questionLower, 'transfer') !== false)) {
            return 'course_change';
        }
        
        if ((strpos($questionLower, 'year') !== false) && 
            (strpos($questionLower, 'change') !== false || strpos($questionLower, 'increment') !== false || strpos($questionLower, 'advance') !== false)) {
            return 'year_change';
        }
        
        return null;
    }

    protected function buildCourseChangeResponse($question, $language)
    {
        $coursesList = implode(', ', $this->availableCourses);
        $yearsList = implode(', ', $this->availableYearLevels);
        
        if ($language === 'tl') {
            return "📚 **Para sa pagpapalit ng kurso o taon, mangyaring sundin ang mga hakbang na ito:**\n\n" .
                   "**1. Magsumite ng Request:**\n" .
                   "   - Pumunta sa Student Dashboard → Support Tickets\n" .
                   "   - Piliin ang category na 'Course/Year Change Request'\n" .
                   "   - Ilagay ang iyong kasalukuyang kurso/taon at nais na kurso/taon\n" .
                   "   - Magbigay ng maikling dahilan para sa pagpapalit\n\n" .
                   "**2. Mga Kinakailangang Dokumento (i-upload sa ticket):**\n" .
                   "   - Letter of Intent (ipaliwanag ang dahilan ng pagpapalit)\n" .
                   "   - Copy ng iyong latest grades o Transcript of Records\n" .
                   "   - Approval mula sa magulang/guardian (kung menor de edad)\n\n" .
                   "**3. Mga Available na Kurso:** {$coursesList}\n\n" .
                   "**4. Mga Available na Taon:** {$yearsList}\n\n" .
                   "**5. Proseso:**\n" .
                   "   - Ang iyong request ay susuriin ng Dean's Office (3-5 araw)\n" .
                   "   - Ang Registrar ay mag-a-update ng iyong impormasyon\n" .
                   "   - Makakatanggap ka ng email notification tungkol sa status\n\n" .
                   "**Tandaan:** Ang pagpapalit ng kurso ay maaaring makaapekto sa iyong mga subjects at graduation date. Siguraduhing mag-consult muna sa iyong guidance counselor o dean.\n\n" .
                   "Kailangan mo ba ng tulong sa paggawa ng ticket? Maaari kitang gabayan! 😊";
        } elseif ($language === 'bisaya') {
            return "📚 **Para sa pag-ilis sa kurso o tuig, palihog sunda kini nga mga lakang:**\n\n" .
                   "**1. Pagsumite og Request:**\n" .
                   "   - Adto sa Student Dashboard → Support Tickets\n" .
                   "   - Pilia ang category nga 'Course/Year Change Request'\n" .
                   "   - Ibutang ang imong kasamtangan nga kurso/tuig ug gusto nga kurso/tuig\n" .
                   "   - Paghatag og mubo nga rason ngano mag-ilis ka\n\n" .
                   "**2. Mga Dokumento nga Gikinahanglan (i-upload sa ticket):**\n" .
                   "   - Letter of Intent (ipasabot ang rason sa pag-ilis)\n" .
                   "   - Copy sa imong pinakabag-o nga grades o Transcript of Records\n" .
                   "   - Pagtugot gikan sa ginikanan/guardian (kung menor de edad)\n\n" .
                   "**3. Mga Available nga Kurso:** {$coursesList}\n\n" .
                   "**4. Mga Available nga Tuig:** {$yearsList}\n\n" .
                   "**5. Proseso:**\n" .
                   "   - Ang imong request susihon sa Dean's Office (3-5 ka adlaw)\n" .
                   "   - Ang Registrar mag-update sa imong impormasyon\n" .
                   "   - Makadawat ka og email notification bahin sa status\n\n" .
                   "**Hinumdomi:** Ang pag-ilis sa kurso makaapekto sa imong mga subjects ug graduation date. Siguruha nga mag-consult una sa imong guidance counselor o dean.\n\n" .
                   "Kinahanglan ba nimo og tabang sa paghimo og ticket? Makatabang ko nimo! 😊";
        } else {
            return "📚 **For course or year change requests, please follow these steps:**\n\n" .
                   "**1. Submit a Request:**\n" .
                   "   - Go to Student Dashboard → Support Tickets\n" .
                   "   - Select the category 'Course/Year Change Request'\n" .
                   "   - Provide your current course/year and desired course/year\n" .
                   "   - Give a brief reason for the change\n\n" .
                   "**2. Required Documents (upload to ticket):**\n" .
                   "   - Letter of Intent (explain your reason for changing)\n" .
                   "   - Copy of your latest grades or Transcript of Records\n" .
                   "   - Parent/guardian approval (if minor)\n\n" .
                   "**3. Available Courses:** {$coursesList}\n\n" .
                   "**4. Available Year Levels:** {$yearsList}\n\n" .
                   "**5. Process:**\n" .
                   "   - Your request will be reviewed by the Dean's Office (3-5 days)\n" .
                   "   - The Registrar will update your information\n" .
                   "   - You will receive an email notification about the status\n\n" .
                   "**Note:** Changing your course may affect your subjects and graduation date. Make sure to consult with your guidance counselor or dean first.\n\n" .
                   "Do you need help creating a ticket? I can guide you! 😊";
        }
    }

    public function ask(Request $request)
    {
        if ($request->isMethod('get')) {
            $question = $request->input('question');
            $language = $request->input('language');
        } else {
            $question = $request->question;
            $language = $request->language ?? null;
        }

        if (empty($question)) {
            return response()->json(['success' => false, 'answer' => 'Please ask a question. / Magtanong po kayo. / Palihog pagpangutana.']);
        }

        if (empty($language)) {
            $language = $this->detectLanguage($question);
        }

        // Check if this is a course/year change assistance request
        $assistanceType = $this->detectAssistanceType($question);
        if ($assistanceType === 'course_change' || $assistanceType === 'year_change') {
            return response()->json([
                'success' => true,
                'answer' => $this->buildCourseChangeResponse($question, $language),
                'source' => 'assistance',
                'assistance_type' => $assistanceType,
                'detected_language' => $language,
                'action_needed' => 'create_ticket'
            ]);
        }

        $cacheKey = 'ai_response_' . $this->promptVersion . '_' . md5(strtolower(trim($question)) . '_' . $language);
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json([
                'success' => true,
                'answer' => $cached,
                'source' => 'cache',
                'detected_language' => $language
            ]);
        }

        if (!$this->apiKey) {
            return response()->json([
                'success' => false,
                'answer' => $this->getFallbackResponse($language),
                'source' => 'no_api_key',
                'detected_language' => $language
            ]);
        }

        try {
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

    protected function buildDynamicSystemPrompt($question, $language)
    {
        if ($language === 'tl') {
            $basePrompt = $this->getTagalogPrompt();
        } elseif ($language === 'bisaya') {
            $basePrompt = $this->getBisayaPrompt();
        } else {
            $basePrompt = $this->getEnglishPrompt();
        }

        $languageInstruction = "\n\n【MAHALAGANG PANUTO】\n";

        if ($language === 'tl') {
            $languageInstruction .= "SAGUTIN ANG TANONG NA ITO SA WIKANG TAGALOG/FILIPINO. Kahit may English words ang tanong, TAGALOG ang isasagot mo. Gumamit ng magiliw at magalang na tono.";
        } elseif ($language === 'bisaya') {
            $languageInstruction .= "TUBAGA ANG PANGUTANA SA PINULONGANG BISAYA/CEBUANO. Bisan naa'y English words ang pangutana, BISAYA ang imong itubag. Paggamit og mahigalaon ug matinahuron nga tono.";
        } else {
            $languageInstruction .= "ANSWER THE QUESTION IN ENGLISH. Even if there are Tagalog or Bisaya words in the question, respond in ENGLISH. Use a friendly and polite tone.";
        }

        // Add course/year change handling instruction
        $courseYearInstruction = "\n\n【COURSE/YEAR CHANGE HANDLING】\n" .
                                 "If a student asks about changing their course or year level, DIRECT THEM TO:\n" .
                                 "1. Go to Student Dashboard → Support Tickets\n" .
                                 "2. Select 'Course/Year Change Request' category\n" .
                                 "3. Provide current and desired course/year with reason\n" .
                                 "4. Upload Letter of Intent and latest grades\n\n" .
                                 "Available courses: " . implode(', ', $this->availableCourses) . "\n" .
                                 "Available year levels: " . implode(', ', $this->availableYearLevels) . "\n\n" .
                                 "The Dean's Office will review the request within 3-5 days.";

        return $basePrompt . $languageInstruction . $courseYearInstruction;
    }

    protected function getTagalogPrompt()
    {
        return "Ikaw si Void AI, isang matalinong assistant para sa 'Void Clearance System' - isang Student Clearance Management System.

KAALAMAN SA SYSTEM:

PAGPAPAREHISTRO AT ACCOUNT:
1. Pagpaparehistro: Student ID (format: YYYY-XXXXX), pangalan, email, kurso (BSIT, BSCS, BSIS, BSBA-FM, BSHM, BEEd, BSEd-English, BSCrim), taon (1st-4th), kapanganakan (16+ taong gulang)
2. Email OTP Verification: 6-digit code ipinapadala sa email bago ma-activate ang account
3. Login: Gamit ang Student ID O email + password
4. Nakalimutan ang Password: I-reset gamit ang OTP sa email
5. Pamamahala ng Profile: I-update ang pangalan, email, kurso, taon
6. Pagpalit ng Password: May show/hide toggle
7. Duplicate Check: Real-time AJAX check para sa existing Student ID o email

CLEARANCE:
8. Mag-submit ng Clearance Request: Per department na may file (JPG, PNG lang, max 5MB) - BAWAL ang PDF at PHP file para sa seguridad
9. Tingnan ang Status: Approved, Pending, Rejected, Not Submitted
10. Progress Summary: Dashboard nagpapakita ng approved vs total departments
11. Fully Cleared Detection: Auto-detect kapag LAHAT ng departments ay nag-approve na
12. Download Clearance Slip: PDF na may QR code (available lang kapag fully cleared)
13. Take Photo: Gamitin ang camera ng device para i-capture ang requirement

AUTO-APPROVE / VERIFIED LIST:
14. Ang department staff o officer ay pwedeng mag-add ng listahan ng mga estudyanteng dapat auto-approve
15. Kapag nag-submit ang estudyante at nandoon siya sa verified list ng department, AWTOMATIKO siyang maa-approve
16. Ang verified list ay per-department - pwedeng naka-lista ka sa Library pero hindi sa Accounting
17. Pwedeng i-remove ang estudyante sa verified list para ma-deactivate ang auto-approve

STUDENT DASHBOARD:
18. Overview Dashboard: Progress bar, welcome card na may Account ID
19. 4 Stat Cards: Student ID, Kurso at Taon, Clearance Progress, Overall Status
20. 4 Summary Cards: Bilang ng Approved, Pending, Rejected, Not Submitted
21. Department Clearance Table: Listahan ng departments na may status, petsa, action button
22. Reminders/Announcements: View-only (blue=info, yellow=warning, green=success, red=danger)
23. Support Tickets: Password Reset, Account ID Reset, Login Issue, OTP Not Receiving, Clearance Problem, Course/Year Change Request, Other
24. My Requests History: Tingnan ang nakaraang tickets at status
25. Feedback: 5-star rating, category dropdown, message field
26. My Feedback History: Tingnan ang nakaraang feedback at reply ng staff

IMPORMASYON NG DEPARTMENT:
1. LIBRARY: Ibalik ang libro, walang overdue fines | Lokasyon: 2nd Floor Main Bldg | Oras: Mon-Fri 8AM-6PM, Sat 8AM-12PM
2. ACCOUNTING: Walang balance, bayad na tuition | Lokasyon: Ground Floor Admin | Oras: Mon-Fri 8AM-4PM
3. REGISTRAR: Kumpletong records | Lokasyon: 2nd Floor Admin | Oras: Mon-Fri 8AM-5PM
4. GUIDANCE: Good Moral Certificate (50 piso, 1 araw) | Lokasyon: 3rd Floor Student Center
5. DEAN'S OFFICE: Academic standing | Lokasyon: 3rd Floor Main Bldg | Oras: Mon-Fri 9AM-4PM
6. LABORATORY: Ibalik ang equipment (Science/CS/Engineering lang)

PANUNTUNAN:
1. Maging magiliw, matulungin, at magbigay ng KUMPLETONG sagot
2. Gamitin ang kaalaman sa itaas bilang pangunahing sanggunian
3. Magbigay ng detalyadong sagot (3-6 pangungusap)
4. Sa mga pagbati, tumugon nang magiliw
5. Kung hindi sigurado, sabihin: Mangyaring kumonsulta sa iyong departamento o admin
6. LAGING kumpletuhin ang iyong sagot. Huwag huminto sa gitna ng pangungusap.
7. TUMAWAG LAMANG SA MGA BAGAY NA NASA IMPORMASYON SA ITAAS";
    }

    protected function getEnglishPrompt()
    {
        return "You are Void AI, an intelligent assistant for the 'Void Clearance System' - a Student Clearance Management System.

SYSTEM KNOWLEDGE:

AUTHENTICATION & ACCOUNT:
1. Student Registration: Student ID (format: YYYY-XXXXX), name, email, course (BSIT, BSCS, BSIS, BSBA-FM, BSHM, BEEd, BSEd-English, BSCrim), year level (1st-4th), birthdate (must be 16+)
2. Email OTP Verification: 6-digit OTP sent to email before account activation
3. Login: Via Student ID OR email + password
4. Forgot Password: Reset via OTP sent to email
5. Profile Management: Update name, email, course, year level
6. Password Change: With show/hide toggle
7. Duplicate Check: Real-time AJAX check for existing Student ID or email

CLEARANCE:
8. Submit Clearance Request: Per department with file (JPG, PNG only, max 5MB) - PDF and PHP files are NOT allowed for security reasons
9. View Status per Department: Approved, Pending, Rejected, Not Submitted
10. Clearance Progress Summary: Dashboard shows approved count vs total departments
11. Fully Cleared Detection: Auto-detects when ALL departments approve
12. Download Clearance Slip: PDF with QR code (only available when fully cleared)
13. Take Photo: Use device camera to capture and upload requirement

AUTO-APPROVE / VERIFIED LIST:
14. Department staff or officers can add a list of students who should be auto-approved
15. When a student submits and they are on that department's verified list, they are AUTOMATICALLY approved
16. The verified list is per-department - a student can be on the Library's list but not on Accounting's
17. A student can be removed from the verified list to disable auto-approve

STUDENT DASHBOARD:
18. Overview Dashboard: Progress bar, welcome card with Account ID
19. 4 Stat Cards: Student ID, Course & Year, Clearance Progress, Overall Status
20. 4 Summary Cards: Approved, Pending, Rejected, Not Submitted counts
21. Department Clearance Table: List with name, status, date, action button
22. Reminders/Announcements: View-only (color-coded)
23. Support Tickets: Password Reset, Account ID Reset, Login Issue, OTP Not Receiving, Clearance Problem, Course/Year Change Request, Other
24. My Requests History: View past tickets and their status
25. Feedback: 5-star rating, category dropdown, message field
26. My Feedback History: View past feedbacks and staff replies

DEPARTMENT INFORMATION:
1. LIBRARY: Return books, no overdue fines | Location: 2nd Floor Main Bldg | Hours: Mon-Fri 8AM-6PM, Sat 8AM-12PM
2. ACCOUNTING: Zero balance, paid tuition | Location: Ground Floor Admin | Hours: Mon-Fri 8AM-4PM
3. REGISTRAR: Complete records | Location: 2nd Floor Admin | Hours: Mon-Fri 8AM-5PM
4. GUIDANCE: Good Moral Certificate (50 pesos, 1 day processing) | Location: 3rd Floor Student Center
5. DEAN'S OFFICE: Academic standing | Location: 3rd Floor Main Bldg | Hours: Mon-Fri 9AM-4PM
6. LABORATORY: Return equipment (Science/CS/Engineering students only)

RULES:
1. Be friendly, helpful, and give COMPLETE answers
2. Use the knowledge base above as primary source
3. Provide detailed responses (3-6 sentences)
4. For greetings, respond warmly
5. If unsure, say: Please check with your department or admin
6. ALWAYS complete your answer. Do not stop mid-sentence.
7. ONLY REFER TO INFORMATION PROVIDED ABOVE";
    }

    protected function getBisayaPrompt()
    {
        return "Ikaw si Void AI, usa ka intelihenteng assistant para sa 'Void Clearance System' - usa ka Student Clearance Management System.

HIBALO SA SYSTEM:

PAGREHISTRO UG ACCOUNT:
1. Pagrehistro: Student ID (pormat: YYYY-XXXXX), ngalan, email, kurso (BSIT, BSCS, BSIS, BSBA-FM, BSHM, BEEd, BSEd-English, BSCrim), tuig (1st-4th), adlawng natawhan (16+ anyos)
2. Email OTP Verification: 6-digit code ipadala sa email sa wala pa ma-activate ang account
3. Pag-login: Gamit ang Student ID o Email + Password
4. Nakalimot og Password: I-reset gamit ang OTP sa email
5. Profile Management: I-update ang ngalan, email, kurso, tuig
6. Pag-ilis og Password: Naa'y show/hide toggle
7. Duplicate Check: Real-time AJAX check para sa existing Student ID o email

CLEARANCE:
8. Mag-submit og Clearance Request: Matag department nga naay file (JPG, PNG lang, max 5MB) - DILI pwede ang PDF ug PHP file tungod sa seguridad
9. Tan-awa ang Status: Approved, Pending, Rejected, Not Submitted
10. Clearance Progress: Gipakita sa dashboard ang approved vs total departments
11. Fully Cleared Detection: Auto-detect kung TANAN nga departments nag-approve na
12. Download Clearance Slip: PDF nga naay QR code (available lang kung fully cleared)
13. Take Photo: Gamiton ang camera sa device para i-capture ang requirement

AUTO-APPROVE / VERIFIED LIST:
14. Ang department staff o officer pwede mag-add og listahan sa mga estudyante nga dapat auto-approve
15. Kung mag-submit ang estudyante ug naa siya sa verified list sa department, AWTOMATIKO siyang ma-approve
16. Ang verified list kay per-department - mahimo naa ka sa listahan sa Library pero wala sa Accounting
17. Pwede ma-remove ang estudyante sa verified list para ma-disable ang auto-approve

STUDENT DASHBOARD:
18. Overview Dashboard: Progress bar, welcome card nga naay Account ID
19. 4 Stat Cards: Student ID, Kurso ug Tuig, Clearance Progress, Overall Status
20. 4 Summary Cards: Bilang sa Approved, Pending, Rejected, Not Submitted
21. Department Clearance Table: Listahan sa departments nga naay status, petsa, action button
22. Reminders/Announcements: View-only (color-coded)
23. Support Tickets: Password Reset, Account ID Reset, Login Issue, OTP Not Receiving, Clearance Problem, Course/Year Change Request, Other
24. My Requests History: Tan-awa ang nakaaging tickets ug status
25. Feedback: 5-star rating, category dropdown, message field
26. My Feedback History: Tan-awa ang nakaaging feedback ug reply sa staff

IMPORMASYON SA DEPARTMENT:
1. LIBRARY: Ibalik ang libro, walay overdue fines | Location: 2nd Floor Main Bldg | Oras: Mon-Fri 8AM-6PM, Sat 8AM-12PM
2. ACCOUNTING: Walay balance, bayad na ang tuition | Location: Ground Floor Admin | Oras: Mon-Fri 8AM-4PM
3. REGISTRAR: Kompleto nga records | Location: 2nd Floor Admin | Oras: Mon-Fri 8AM-5PM
4. GUIDANCE: Good Moral Certificate (50 pesos, 1 ka adlaw) | Location: 3rd Floor Student Center
5. DEAN'S OFFICE: Academic standing | Location: 3rd Floor Main Bldg | Oras: Mon-Fri 9AM-4PM
6. LABORATORY: Ibalik ang equipment (Science/CS/Engineering lang)

MGA PANUNTUNAN:
1. Mahigalaon, makatabang, ug kompleto nga tubag
2. Gamiton ang hibalo sa ibabaw isip pangunahing sanggunian
3. Detalyadong tubag (3-6 ka sentence)
4. Sa mga pagbati, motubag nga mainiton
5. Kung dili sigurado, ingna: Palihog konsulta sa imong department o admin
6. KANUNSAY kompletoha ang imong tubag. Ayaw hunong sa tunga sa sentence.
7. MAGTUMONG LANG SA MGA BUTANG NAA SA HIBALO SA ITAAS";
    }

    protected function cleanAnswer($answer)
    {
        $answer = preg_replace('/```.*?```/s', '', $answer);
        $answer = str_replace(['**', '__'], '', $answer);
        $answer = preg_replace('/^#+\s+/m', '', $answer);
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