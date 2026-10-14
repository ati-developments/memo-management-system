<?php

namespace App\Support;

class MemoTextFormatting
{
    public static function sanitize(string $html): string
    {
        $document = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8"><body>'.$html.'</body>', LIBXML_NONET);
            $render = function (\DOMNode $node) use (&$render): string {
                if ($node instanceof \DOMText) {
                    return htmlspecialchars($node->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                }
                if (!($node instanceof \DOMElement)) return '';
                $tag = strtolower($node->tagName);
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'svg', 'math'], true)) return '';
                $content = '';
                foreach ($node->childNodes as $child) $content .= $render($child);
                $tag = ['b' => 'strong', 'i' => 'em'][$tag] ?? $tag;
                if ($tag === 'br') return '<br>';
                if ($tag === 'span') {
                    $size = $node->getAttribute('style');
                    if (preg_match('/^\s*font-size\s*:\s*(12px|14px|16px|18px|24px)\s*;?\s*$/i', $size, $match)) {
                        return '<span style="font-size:'.$match[1].'">'.$content.'</span>';
                    }
                    return $content;
                }
                if (in_array($tag, ['strong', 'em', 'u', 'p', 'div', 'ul', 'ol', 'li', 'h2', 'h3', 'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td'], true)) return '<'.$tag.'>'.$content.'</'.$tag.'>';
                return $content;
            };
            $body = $document->getElementsByTagName('body')->item(0);
            $result = '';
            foreach ($body->childNodes as $child) $result .= $render($child);
            return $result;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }
}
