<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AboutBaseController extends AbstractController
{
    protected function getSubMenuItems(string $locale): array
    {
        return [
            'about' => [
                'key' => 'AboutUsSubmenu',
                'url' => $this->generateUrl('about'),
            ],
            'about_faq' => [
                'key' => 'Faq',
                'url' => $this->generateUrl('about_faq'),
            ],
            'about_feedback' => [
                'key' => 'ContactUs',
                'url' => $this->generateUrl('contactus'),
            ],
            'separator' => [
                'key' => 'About_AtAGlance',
                'url' => '',
            ],
            'about_theidea' => [
                'key' => 'About_TheIdea',
                'url' => $this->generateUrl('about_theidea'),
            ],
            'about_people' => [
                'key' => 'About_ThePeople',
                'url' => $this->generateUrl('about_people'),
            ],
            'getactive' => [
                'key' => 'About_GetActive',
                'url' => $this->generateUrl('getactive'),
            ],
            'separator2' => [
                'key' => 'MoreInfo',
                'url' => '',
            ],
            'about_press' => [
                'key' => 'PressInfoPage',
                'url' => $this->generateUrl('about_press'),
            ],
            'about_bod' => [
                'key' => 'BoardOfDirectorsPage',
                'url' => $this->generateUrl('about_bod'),
            ],
            'about_bv' => [
                'key' => 'BeVolunteerBlogs',
                'url' => $this->generateUrl('about_bv'),
            ],
            'about_terms' => [
                'key' => 'TermsPage',
                'url' => $this->generateUrl('terms_of_use', [
                    'locale' => $locale,
                ]),
            ],
            'about_privacy' => [
                'key' => 'PrivacyPage',
                'url' => $this->generateUrl('privacy_policy', [
                    'locale' => $locale,
                ]),
            ],
            'about_commentguidelines' => [
                'key' => 'CommentGuidelinesPage',
                'url' => $this->generateUrl('profilecomments'),
            ],
            'statistics' => [
                'key' => 'StatsPage',
                'url' => $this->generateUrl('about_statistics'),
            ],
            'about_credits' => [
                'key' => 'credits.title',
                'url' => $this->generateUrl('about_credits'),
            ],
        ];
    }
}
