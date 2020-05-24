<?php

namespace Rxx;

class Changes
{
    /**
     * @return string
     */
    public function draw()
    {

        $entries =      $this->getGitInfo();
        $tweaks = [
            [
                'Darek K',
                'Joachim Rabe',
                'Joachim',
                '[',
                ']'
            ],
            [
                '[Darek K]',
                '[Joachim Rabe]',
                '[Joachim Rabe]',
                '<span>',
                '</span>'
            ]
        ];
        $changelog =
            "<ul class='changelog'><li>"
            . str_replace($tweaks[0], $tweaks[1], implode("</li>\n<li>", $entries))
            . "</li></ul>";

        return
             "<h2>Change Log for this system<br>"
            ."<span style=\"font-size:75%\">" . count($entries) ." versioned releases since 2015-03-31<br>"
            ."(First ever version was released in 2004)</span></h2>"
            ."<p>This page gives headline information about changes to this system over time.</p>"
            ."<p>If you spot a bug or have ideas for a new feature, please contact me - you may become famous in the release notes!</p>"
            .$changelog;
    }

    public function getGitInfo()
    {
        $changelog = explode("\n", `git log master --pretty=format:"%ad %s" --date=short`);
        $entries = [];
        foreach ($changelog as &$entry) {
            $bits =     explode(' ', $entry);
            $date =     array_shift($bits);
            $version =  trim(array_shift($bits), ':');
            $details =  implode(' ', $bits);
            $entries[] =    '<strong>'.$version.'</strong> <em>('.$date.')</em><br />'.$details;
        }
        return $entries;
    }

}
