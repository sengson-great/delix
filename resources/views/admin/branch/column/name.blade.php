<div>
    {{ $query->name }}
    <br>
    {{ isDemoMode() ? '**************' :  $query->phone_number ?? '' }}
</div>
