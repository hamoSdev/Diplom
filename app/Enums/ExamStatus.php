<?php

namespace App\Enums;

enum ExamStatus: string
{
    case InProgress = 'in_progress';
    case Finished = 'finished';
}
