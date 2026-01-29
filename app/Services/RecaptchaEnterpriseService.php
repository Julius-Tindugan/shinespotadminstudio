<?php

namespace App\Services;

use Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\CreateAssessmentRequest;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;
use Illuminate\Support\Facades\Log;

class RecaptchaEnterpriseService
{
    private string $siteKey;
    private string $projectId;
    private float $minimumScore;

    public function __construct()
    {
        $this->siteKey = config('captcha.enterprise.sitekey');
        $this->projectId = config('captcha.enterprise.project_id');
        $this->minimumScore = config('captcha.enterprise.minimum_score', 0.5);
    }

    /**
     * Validate reCAPTCHA Enterprise token
     *
     * @param string $token The reCAPTCHA token from the client
     * @param string $action The action associated with the token (e.g., 'login', 'register')
     * @return array Validation result with success status and details
     */
    public function validateToken(string $token, string $action = 'submit'): array
    {
        try {
            // Create the reCAPTCHA client
            $client = new RecaptchaEnterpriseServiceClient();
            $projectName = $client->projectName($this->projectId);

            // Set the properties of the event to be tracked
            $event = (new Event())
                ->setSiteKey($this->siteKey)
                ->setToken($token);

            // Build the assessment request
            $assessment = (new Assessment())
                ->setEvent($event);

            $request = (new CreateAssessmentRequest())
                ->setParent($projectName)
                ->setAssessment($assessment);

            $response = $client->createAssessment($request);

            // Check if the token is valid
            if (!$response->getTokenProperties()->getValid()) {
                $invalidReason = $response->getTokenProperties()->getInvalidReason();
                Log::warning('reCAPTCHA Enterprise token invalid', [
                    'reason' => InvalidReason::name($invalidReason),
                    'action' => $action
                ]);

                return [
                    'success' => false,
                    'message' => 'reCAPTCHA validation failed: Invalid token',
                    'reason' => InvalidReason::name($invalidReason)
                ];
            }

            // Check if the expected action was executed
            if ($response->getTokenProperties()->getAction() !== $action) {
                Log::warning('reCAPTCHA Enterprise action mismatch', [
                    'expected' => $action,
                    'received' => $response->getTokenProperties()->getAction()
                ]);

                return [
                    'success' => false,
                    'message' => 'reCAPTCHA validation failed: Action mismatch',
                    'expected_action' => $action,
                    'received_action' => $response->getTokenProperties()->getAction()
                ];
            }

            // Get the risk score
            $score = $response->getRiskAnalysis()->getScore();
            
            Log::info('reCAPTCHA Enterprise validation completed', [
                'score' => $score,
                'action' => $action,
                'minimum_score' => $this->minimumScore
            ]);

            // Check if score meets minimum threshold
            if ($score < $this->minimumScore) {
                return [
                    'success' => false,
                    'message' => 'reCAPTCHA validation failed: Risk score too low',
                    'score' => $score,
                    'minimum_score' => $this->minimumScore
                ];
            }

            return [
                'success' => true,
                'message' => 'reCAPTCHA validation successful',
                'score' => $score,
                'action' => $action
            ];

        } catch (\Exception $e) {
            Log::error('reCAPTCHA Enterprise validation error', [
                'error' => $e->getMessage(),
                'action' => $action
            ]);

            return [
                'success' => false,
                'message' => 'reCAPTCHA validation error: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate reCAPTCHA for login action
     *
     * @param string $token
     * @return array
     */
    public function validateLogin(string $token): array
    {
        return $this->validateToken($token, 'login');
    }

    /**
     * Validate reCAPTCHA for registration action
     *
     * @param string $token
     * @return array
     */
    public function validateRegistration(string $token): array
    {
        return $this->validateToken($token, 'register');
    }
}