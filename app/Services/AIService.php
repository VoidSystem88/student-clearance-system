<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    }

    public function ask($question, $language = 'en')
    {
        // Check cache first (para hindi paulit-ulit sa API)
        $cached = $this->getCachedResponse($question);
        if ($cached) {
            return $cached;
        }
        
        // Check if API key is available
        if (!$this->apiKey || $this->apiKey === 'YOUR_API_KEY' || $this->apiKey === '') {
            return $this->getOfflineResponse($question, $language);
        }
        
        try {
            $response = Http::timeout(15)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $this->buildPrompt($question, $language)
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 500,
                    'topP' => 0.95,
                ]
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                
                if ($answer) {
                    // Clean up the answer
                    $answer = $this->cleanAnswer($answer);
                    $this->cacheResponse($question, $answer);
                    return $answer;
                }
            }
            
            // Log error for debugging
            Log::warning('Gemini API returned error: ' . $response->body());
            return $this->getOfflineResponse($question, $language);
            
        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            return $this->getOfflineResponse($question, $language);
        }
    }
    
    protected function buildPrompt($question, $language)
    {
        $langName = $this->getLanguageName($language);
        
        return "You are Void AI, an assistant for a Student Clearance Management System.

=== SYSTEM KNOWLEDGE BASE (USE THIS AS PRIMARY SOURCE) ===

1. WHAT IS CLEARANCE?
   - A document proving no outstanding balances or pending requirements
   - Required for: enrollment, graduation, school transfer
   - Must be cleared by ALL departments

2. DEPARTMENTS TO CLEAR:
   - Library (return all books, no overdue fines)
   - Accounting / Cashier (zero balance, paid tuition)
   - Registrar (complete records, correct info)
   - Guidance (good moral certificate, exit interview if graduating)
   - Dean's Office (academic standing, subject completion)
   - Laboratory (return equipment, no damages - for science/computer courses)

3. REQUIREMENTS:
   - Complete Student Information Form
   - Clearance Application Form
   - Valid Student ID
   - No outstanding balance
   - Good Moral Certificate

4. HOW TO APPLY:
   - Step 1: Go to Clearance page from sidebar
   - Step 2: Click Apply for Clearance button
   - Step 3: Fill out the application form
   - Step 4: Review your information
   - Step 5: Submit your application

5. HOW TO SUBMIT DOCUMENTS:
   - Step 1: Go to Clearance page
   - Step 2: Click on department section
   - Step 3: Click Upload Document button
   - Step 4: Select JPG, PNG, or PDF file (max 5MB)
   - Step 5: Click Submit for Review
   - Alternative: Use Take Photo button for camera capture

6. STATUS MEANINGS:
   - Approved: Department has accepted your submission ✅
   - Pending: Waiting for staff to review ⏳
   - Rejected: Needs resubmission, check remarks ❌
   - Not Submitted: No document uploaded yet 📄

7. PROCESSING TIME:
   - Document review: 2-3 business days
   - Department approval: 1-2 days per department
   - Full clearance: Typically 1-2 weeks

8. FEATURES:
   - Online clearance application
   - Document upload (JPG/PNG/PDF)
   - Take photo using camera
   - Real-time status tracking
   - Notifications and reminders
   - Clearance history
   - Download clearance slip (PDF)
   - Feedback and rating system

=== RULES ===
1. Be friendly and helpful
2. Answer in {$langName} language only
3. Use the knowledge base above as your primary source
4. Keep responses concise (maximum 3-4 sentences for simple questions)
5. If the question is about the system, ALWAYS use the knowledge base
6. For greetings (hello, hi, kamusta), respond warmly
7. If unsure, say \"I'm not sure, please check with your department or admin\"

=== USER QUESTION ===
{$question}

=== YOUR ANSWER (in {$langName}) ===";
    }
    
    protected function cleanAnswer($answer)
    {
        // Remove any markdown code blocks
        $answer = preg_replace('/```.*?```/s', '', $answer);
        // Remove any leftover asterisks that might break HTML
        $answer = str_replace(['**', '__'], '', $answer);
        // Convert newlines to <br> for HTML display
        $answer = nl2br(trim($answer));
        
        return $answer;
    }
    
    protected function getLanguageName($code)
    {
        return match($code) {
            'tl' => 'Tagalog',
            'bisaya' => 'Bisaya/Cebuano',
            default => 'English'
        };
    }
    
    protected function cacheResponse($question, $answer)
    {
        $key = 'ai_cache_' . md5(strtolower(trim($question)));
        Cache::put($key, $answer, 86400); // 24 hours cache
    }
    
    protected function getCachedResponse($question)
    {
        $key = 'ai_cache_' . md5(strtolower(trim($question)));
        return Cache::get($key);
    }
    
    protected function getOfflineResponse($question, $language)
    {
        $q = strtolower($question);
        
        // Local fallback responses
        $responses = [
            'hello' => "Hello! 👋 I'm Void AI. How can I help with your clearance today?",
            'hi' => "Hi there! 👋 Need help with your clearance?",
            'kamusta' => "I'm good! Thanks for asking. How can I help you with your clearance? 😊",
            'help' => "I can help you with:\n• Clearance requirements\n• How to apply for clearance\n• Document submission guide\n• Status tracking\n• Answering your questions",
            'clearance' => "Clearance is a document required for enrollment, graduation, and school transfer. You need approval from all departments.",
            'requirements' => "Requirements: Student Information Form, Clearance Application, Valid Student ID, No outstanding balance, Good Moral Certificate.",
            'how to apply' => "Go to Clearance page, click Apply for Clearance, fill out the form, and submit your application.",
            'how to submit' => "Upload JPG, PNG, or PDF files (max 5MB). You can also take a photo using the camera button.",
            'status' => "Check your Dashboard for clearance progress. Green means approved, yellow means pending.",
            'departments' => "Departments: Library, Accounting, Registrar, Guidance, Dean's Office, and Laboratory.",
            'processing time' => "Document review takes 2-3 business days. Full clearance typically takes 1-2 weeks.",
        ];
        
        foreach ($responses as $key => $response) {
            if (strpos($q, $key) !== false) {
                return $response;
            }
        }
        
        if ($language === 'tl') {
            return "Hello! 👋 Ako si Void AI. Ano'ng kailangan mong malaman tungkol sa clearance? Pwede kitang tulungan sa requirements, pag-apply, at pag-submit ng documents.";
        } elseif ($language === 'bisaya') {
            return "Hello! 👋 Ako si Void AI. Unsa imong gustong mahibaw-an bahin sa clearance? Makatabang ko nimo sa requirements, pag-apply, ug pag-submit og documents.";
        }
        
        return "Hello! 👋 I'm Void AI. I can help you with clearance requirements, how to apply, document submission, and status tracking. What would you like to know?";
    }
}