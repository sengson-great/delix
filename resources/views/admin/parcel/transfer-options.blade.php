<option value="">{{ __('select_branch') }}</option>
@foreach($branchs as $branch)
    <option value="{{ $branch->id }}">{{ $branch->name.' ('.$branch->address }})</option>
@endforeach
1400.0*9+-65260
