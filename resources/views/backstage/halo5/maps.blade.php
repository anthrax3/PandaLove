@extends('app')

@section('content')
    <div class="wrapper style1">
        <article class="container" id="top">
            <div class="row">
                <div class="12u">
                    @include('includes.backstage.menu')
                    <br />
                    <h3 class="ui header">Map Generator</h3>
                    <div class="ui red message" style="display:none;" id="gen_error">
                        <div class="header">
                            Error!
                        </div>
                        <p id="err_message"></p>
                    </div>
                    <div class="ui blue raised segment">
                        {!! Form::open(['class' => 'ui inline form', 'id' => 'map_form']) !!}
                        <input type="hidden" value="" name="type" id="type">
                        <div class="three fields equal width">
                            <div class="field">
                                <div class="ui fluid search selection dropdown">
                                    <input type="hidden" name="map_id" id="map_id" value="">
                                    <i class="dropdown icon"></i>
                                    <div class="default text">Map</div>
                                    <div class="menu">
                                        @foreach ($maps as $map)
                                            <div class="item" data-value="{{ $map['uuid'] }}">{{ $map['name'] }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="field">
                                <input type="number" name="x_orig" id="x_orig" placeholder="X Origin">
                            </div>
                            <div class="field">
                                <input type="number" name="x_scale" id="x_scale" placeholder="X Scale" step="0.0001">
                            </div>
                        </div>
                        <div class="three fields equal width">
                            <div class="field">
                                <input type="number" name="num_games" id="num_games" placeholder="# Games" min="0">
                            </div>
                            <div class="field">
                                <input type="number" name="y_orig" id="y_orig" placeholder="Y Origin">
                            </div>
                            <div class="field">
                                <input type="number" name="y_scale" id="y_scale" placeholder="Y Scale" step="0.0001">
                            </div>
                        </div>
                        <button type="submit" class="ui blue button" id="gen_btn">Generate Map</button>
                        <button type="submit" class="ui green button" id="save_btn">Save Scaling</button>
                        {!! Form::close() !!}
                    </div>

                    <img id="generated_map" src="" />
                </div>
            </div>
        </article>
    </div>
@endsection

@section('inline-js')
    <script type="text/javascript">
        $(document).on('ready', function() {
            $('.ui.dropdown').dropdown({
                onChange : function(value, text, $choice) {
                    $('#map_id').val(value);
                }
            });

            $('#gen_btn').on('click', function(){
                $('#type').val('generate');
            });

            $("#save_btn").on('click', function() {
                $('#type').val('save');
            });

            $('.ui.form').form({
                fields: {
                    map: 'empty',
                    x_orig: 'integer',
                    x_scale: 'number',
                    num_games: 'integer[1..100]',
                    y_orig: 'integer',
                    y_scale: 'number'
                },
                onSuccess: function(event) {
                    event.preventDefault();

                    $.ajax({
                        type: 'POST',
                        url: '{{ action('Backstage\Halo5Controller@postMaps') }}',
                        data: $('.ui.form').form('get values'),
                        success: function(data) {
                            if (data[0]['error'] != true) {
                                $('#gen_error').hide();
                                $('#generated_map').attr('src', data[0]['image']['encoded']);
                            } else {
                                $('#gen_error').show();
                                $('#err_message').text(data[0]['message']);
                            }
                        }
                    });
                }
            });
        });
    </script>
@append