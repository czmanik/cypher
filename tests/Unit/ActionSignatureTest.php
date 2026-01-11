<?php

namespace Tests\Unit;

use App\Filament\Actions\ViewShiftHistoryAction;
use PHPUnit\Framework\TestCase;

class ActionSignatureTest extends TestCase
{
    public function test_make_method_signature()
    {
        // This test mainly verifies that the class can be loaded and the method called without fatal error.
        $action = ViewShiftHistoryAction::make();
        $this->assertEquals('viewHistory', $action->getName());

        $action2 = ViewShiftHistoryAction::make('customName');
        $this->assertEquals('customName', $action2->getName());
    }
}
