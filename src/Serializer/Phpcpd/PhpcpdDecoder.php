<?php

namespace App\Serializer\Phpcpd;

use App\Domain\Vendors;
use Symfony\Component\Serializer\Encoder\ContextAwareDecoderInterface;

class PhpcpdDecoder implements ContextAwareDecoderInterface
{
    public function decode(string $data, string $format, array $context = [])
    {
        $decoded = [];

        preg_match_all(
            '/(?P<clones>(\d+)) clones with (?P<duplicates>(\d+)) duplicated lines in (?P<files>(\d+))/im',
            $data,
            $count
        );
        $decoded['count']['clones'] = $count['clones'][0] ?? 0;
        $decoded['count']['duplicates'] = $count['duplicates'][0] ?? 0;
        $decoded['count']['files'] = $count['files'][0] ?? 0;

        preg_match_all(
            '/^(?P<percent>(\d+(\.\d*)?|\.\d+)).* (?<lines>(\d+))/im',
            $data,
            $ratio
        );
        $decoded['ratio']['percent'] = $ratio['percent'][0] ?? 0;
        $decoded['ratio']['total'] = $ratio['lines'][0] ?? 0;

        preg_match_all(
            '/Average size of duplication is (?P<avg>(\d+)) lines, largest clone has (?P<max>(\d+)) of lines/im',
            $data,
            $average
        );
        $decoded['average']['med'] = $average['avg'][0] ?? 0;
        $decoded['average']['max'] = $average['max'][0] ?? 0;

        preg_match_all(
            '/Time: (?P<time>(\d+)) ms, Memory: (?P<memory>(\d+(\.\d*)?|\.\d+)) (?P<unit>(.+))/im',
            $data,
            $bench
        );
        $decoded['bench']['timems'] = $bench['time'][0];
        $decoded['bench']['memval'] = $bench['memory'][0];
        $decoded['bench']['memunit'] = $bench['memory'][0];

        preg_match_all(
            '/ {2,}(- )?\/project\/(?P<file>(.*)):(?P<from>(\d+))-(?P<to>(\d+))( \((?P<length>(\d+)) lines\))?/im',
            $data,
            $cpd
        );

        if (count($cpd['file']) > 0) {
            for ($i = 0; $i < count($cpd['file']); $i++) {
                if (!empty($cpd['length'][$i])) {
                    $decoded['cpd'][] = [
                        'file'     => $cpd['file'][$i],
                        'fromline' => $cpd['from'][$i],
                        'toline'   => $cpd['to'][$i],
                        'length'   => $cpd['length'][$i],
                        'clones'   => [],
                    ];
                }
            }

            $tickPointer = null;
            $groups = [];
            for ($i = 0; $i < count($cpd['file']); $i++) {
                if (!empty($cpd['length'][$i])) {
                    $tickPointer = $i;
                } else {
                    $clone = [
                        'file'     => $cpd['file'][$i],
                        'fromline' => $cpd['from'][$i],
                        'toline'   => $cpd['to'][$i],
                    ];
                    $groups[$tickPointer][] = $clone;
                }
            }

            $groups = array_values($groups);
            for ($i = 0; $i < count($decoded['cpd']); $i++) {
                $decoded['cpd'][$i]['clones'] = $groups[$i];
            }
        }

        return $decoded;
    }

    public function supportsDecoding(string $format, array $context = [])
    {
        return 'txt' === $format
            && !empty($context)
            && !empty($context['vendor'])
            && Vendors::PHPCPD === $context['vendor'];
    }
}
