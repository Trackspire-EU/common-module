<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Tracking;

enum EventType: string
{
    case PAGEVIEW = 'pageview';
    case CUSTOM_EVENT = 'custom_event';
    case PERFORMANCE = 'performance';
    case OUTBOUND = 'outbound';
    case ERROR = 'error';
    case COPY = 'copy';
    case BUTTON_CLICK = 'button_click';
    case FORM_SUBMIT = 'form_submit';
    case INPUT_CHANGE = 'input_change';

    public function getValue(): string
    {
        return $this->value;
    }
}
