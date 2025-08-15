<?php

namespace App\Enums;

enum InactivityStatus: string
{
    case WaitingForApproval = "Válaszra vár";
    case Accepted = "Elfogadva";
    case Declined = "Elutasítva";
}
