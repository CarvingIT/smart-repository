<option value="{{ $taxonomy->id }}" 
    data-pup="{{ $taxonomy->parent_id }}" 
    class="l{{ $level }} @if(count($taxonomy->childs) > 0) non-leaf @endif">{{ $taxonomy->label }}</option>
    @foreach($taxonomy->childs as $ch)
        <x-taxonomy-option :taxonomy="$ch" :level="$level" />
    @endforeach
