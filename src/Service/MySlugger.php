<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class MySlugger
{
    private $slugger;
    private $toLower;

    public function __construct(SluggerInterface $slugger, bool $toLower)
    {
        $this->slugger = $slugger;
        $this->toLower = $toLower;
    }

    public function slugify(string $str)
    {
        if ($this->toLower) {
            return $this->slugger->slug($str)->lower();
        }

        return $this->slugger->slug($str);
    }
}
