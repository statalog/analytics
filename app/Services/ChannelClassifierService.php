<?php

namespace App\Services;

class ChannelClassifierService
{
    const DIRECT   = 'Direct';
    const SEARCH   = 'Search';
    const SOCIAL   = 'Social';
    const AI       = 'AI Assistants';
    const CAMPAIGNS = 'Campaigns';
    const WEBSITES = 'Websites';

    private static array $search = [
        'google.com', 'google.co.uk', 'google.fr', 'google.de', 'google.it',
        'google.es', 'google.ca', 'google.com.au', 'google.co.in', 'google.co.jp',
        'google.ru', 'google.pl', 'google.nl', 'google.com.br', 'google.com.mx',
        'google.ro', 'google.be', 'google.ch', 'google.at', 'google.se',
        'bing.com', 'duckduckgo.com', 'yahoo.com', 'search.yahoo.com',
        'yandex.ru', 'yandex.com', 'yandex.ua', 'baidu.com',
        'ecosia.org', 'ask.com', 'aol.com', 'qwant.com',
        'startpage.com', 'brave.com', 'search.brave.com',
        'naver.com', 'daum.net', 'sogou.com', 'so.com',
        'kagi.com', 'mojeek.com', 'swisscows.com', 'gibiru.com',
    ];

    private static array $social = [
        'facebook.com', 'fb.com', 'm.facebook.com',
        'twitter.com', 'x.com', 't.co',
        'instagram.com', 'l.instagram.com',
        'linkedin.com', 'lnkd.in',
        'reddit.com', 'redd.it', 'old.reddit.com', 'out.reddit.com',
        'pinterest.com', 'pinterest.co.uk', 'pinterest.fr', 'pinterest.de',
        'tiktok.com', 'vm.tiktok.com',
        'youtube.com', 'youtu.be', 'm.youtube.com',
        'snapchat.com',
        'telegram.org', 't.me',
        'whatsapp.com', 'wa.me',
        'discord.com', 'discord.gg',
        'vk.com', 'vkontakte.ru',
        'ok.ru', 'odnoklassniki.ru',
        'tumblr.com',
        'mastodon.social',
        'threads.net',
        'bsky.app', 'bsky.social',
        'quora.com',
        'medium.com',
        'substack.com',
        'hackernews.com', 'news.ycombinator.com',
        'producthunt.com',
        'dev.to',
        'weibo.com', 'weibo.cn',
    ];

    private static array $ai = [
        'chatgpt.com', 'chat.openai.com', 'openai.com',
        'claude.ai', 'anthropic.com',
        'perplexity.ai', 'pplx.ai',
        'gemini.google.com', 'bard.google.com', 'aistudio.google.com',
        'copilot.microsoft.com', 'bing.com/chat',
        'you.com',
        'phind.com',
        'poe.com',
        'character.ai',
        'writesonic.com',
        'jasper.ai',
        'mistral.ai',
        'cohere.com',
        'inflection.ai', 'pi.ai',
        'grok.x.ai', 'x.ai',
        'huggingface.co',
        'replicate.com',
        'groq.com',
    ];

    public function classify(string $referrerDomain, string $utmSource): string
    {
        if ($utmSource !== '') {
            return self::CAMPAIGNS;
        }

        if ($referrerDomain === '') {
            return self::DIRECT;
        }

        $domain = strtolower(preg_replace('/^www\./', '', $referrerDomain));

        if (in_array($domain, self::$ai, true)) {
            return self::AI;
        }

        if (in_array($domain, self::$social, true)) {
            return self::SOCIAL;
        }

        if (in_array($domain, self::$search, true) || $this->isGoogleDomain($domain)) {
            return self::SEARCH;
        }

        return self::WEBSITES;
    }

    private function isGoogleDomain(string $domain): bool
    {
        return preg_match('/^google\.[a-z]{2,6}(\.[a-z]{2})?$/', $domain) === 1;
    }

    public static function allChannels(): array
    {
        return [self::SEARCH, self::SOCIAL, self::AI, self::CAMPAIGNS, self::WEBSITES, self::DIRECT];
    }

    public static function icon(string $channel): string
    {
        return match ($channel) {
            self::SEARCH   => 'search',
            self::SOCIAL   => 'share',
            self::AI       => 'robot',
            self::CAMPAIGNS => 'megaphone',
            self::DIRECT   => 'arrow-right-circle',
            self::WEBSITES => 'link-45deg',
            default        => 'globe2',
        };
    }
}
