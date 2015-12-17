<?php

namespace spec\kriskbx\wyn;

use PhpSpec\ObjectBehavior;

class ApplicationSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('kriskbx\wyn\Application');
    }
}
