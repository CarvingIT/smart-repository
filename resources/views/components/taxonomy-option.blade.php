<option value="{{ $taxonomy->id }}" @if($level != 1) data-pup="{{ $taxonomy->parent_id }}" @endif class="l{{ $level }} 
    @if(count($taxonomy->childs) > 0) non-leaf @endif">{{ $taxonomy->label }}</option>
    @foreach($taxonomy->childs as $ch)
        <x-taxonomy-option :taxonomy="$ch" :level="$level" />
    @endforeach
