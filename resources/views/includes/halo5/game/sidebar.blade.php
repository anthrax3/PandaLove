<div class="ui fluid card">
    <div class="image">
        <img src="{{ $match->map->getImage() }}" />
    </div>
    <div class="content">
        <div class="left floated author">
            <img class="ui avatar image" src="{{ $match->gametype->getImage()}}" />
        </div>
        <div class="right floated">
            <span class="header">{{ $match->gametype->name }} on {{ $match->map->name }}</span>
        </div>
    </div>
</div>
@if (count($match->teams) > 1)
    @foreach ($match->teams as $team)
        <div class="ui inverted {{ $team->team->getSemanticColor() }} segment">
            <img class="ui avatar image" src="{{ $team->team->getImage() }}">
            <span class="header">{!! $team->label() !!} {{ $team->team->name }} - {{ $team->score }}</span>
        </div>
    @endforeach
@else

@endif
<div class="ui black segment">
    <a href="{{ action('Halo5\GameController@getMatchEvents', [$type, $match->uuid]) }}" class="ui black fluid button">Enhanced Game Look</a>
</div>