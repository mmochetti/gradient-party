<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Gradient Party</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600"
            rel="stylesheet"
            type="text/css">

        <link rel="stylesheet" type="text/css" href="css/app.css">

        <!-- Bootstrap theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    </head>
    <body>
        <div id="app" :class="['app', { toggled : editor_toggled }]">
             <div class="editor" v-cloak>
                <div class="options col-sm-12">
                    <div class="col-sm-12">
                        <h4>
                        Edit your gradient
                        <a v-show="editor_toggled" href="javascript:void(0)" @click="editor_toggled = !editor_toggled"
                         :class="[
                          'editor-toggle glyphicon',
                          {'glyphicon-menu-left' : editor_toggled},
                          {'glyphicon-menu-right' : !editor_toggled}
                        ]">
                        </a>
                        </h4>
                    </div>

                    <div class="option col-sm-12">
                      <p class="title">STYLE: </p>
                      <div class="values">
                        <div @click="setOption('style', 'linear')" :class="[ { selected : hasOptionAs('style', 'linear') } , 'radio-button' ]">
                          Linear
                        </div>
                        <div @click="setOption('style', 'radial')" :class="[ { selected : hasOptionAs('style', 'radial') }, 'radio-button' ]">
                          Radial
                        </div>
                      </div>
                    </div>

                    <div class="option col-sm-12">
                      <p class="title">DIRECTION: </p>
                      <div class="values">
                        <div @click="setOption('direction', 'top')" :class="[ { 'selected' : hasOptionAs('direction', 'top') }, 'radio-button' ]">
                          Top
                        </div>
                        <div @click="setOption('direction', 'top right')" :class="[ { 'selected' : hasOptionAs('direction', 'top right') }, 'radio-button' ]">
                          Top right
                        </div>
                        <div @click="setOption('direction', 'right')" :class="[ { 'selected' : hasOptionAs('direction', 'right') }, 'radio-button' ]">
                          Right
                        </div>
                        <div @click="setOption('direction', 'bottom right')" :class="[ { 'selected' : hasOptionAs('direction', 'bottom right') }, 'radio-button' ]">
                          Bottom right
                        </div>
                        <div @click="setOption('direction', 'bottom')" :class="[ { 'selected' : hasOptionAs('direction', 'bottom') }, 'radio-button' ]">
                          Bottom
                        </div>
                        <div @click="setOption('direction', 'bottom left')" :class="[ { 'selected' : hasOptionAs('direction', 'bottom left') }, 'radio-button' ]">
                          Bottom left
                        </div>
                        <div @click="setOption('direction', 'left')" :class="[ { 'selected' : hasOptionAs('direction', 'left') }, 'radio-button' ]">
                          Left
                        </div>
                        <div @click="setOption('direction', 'top left')" :class="[ { 'selected' : hasOptionAs('direction', 'top left') }, 'radio-button' ]">
                          Top left
                        </div>
                      </div>
                    </div>

                    <div class="option col-sm-12">
                      <p class="title">COLORS: </p>
                      <div class="values row">
                        <div class="color-picker col-sm-3 col-xs-4">
                          <div class="picker">
                            <input type="color" name="start_color" @change="sendCSSThroughSocket" v-model="options.start_color">
                          </div>
                          <div class="value">@{{ options.start_color }}</div>
                        </div>

                        <div class="color-picker col-sm-3 col-xs-4">
                          <div class="picker">
                            <input type="color" name="end_color" @change="sendCSSThroughSocket" v-model="options.end_color">
                          </div>
                          <div class="value">@{{ options.end_color }}</div>
                        </div>
                      </div>
                    </div>

                    <div class="result col-sm-12">
                        <!-- gradient color result -->
                        <p class="title">RESULT: </p>
                        <div class="color-box" v-bind:style="cssObject">
                        </div>
                    </div>
                </div>
            </div>

            <div class="main-content" v-cloak>
                <div class="container-fluid">
                  <a v-show="!editor_toggled" href="javascript:void(0)" @click="editor_toggled = !editor_toggled"
                     :class="[
                      'editor-toggle glyphicon',
                      {'glyphicon-menu-left' : editor_toggled},
                      {'glyphicon-menu-right' : !editor_toggled}
                    ]">
                  </a>

                  <h1 class="text-center">Gradient PARTY!</h1>
                  <h4 class="text-center">
                    Write your name here:
                    <input placeholder="John Snow"
                    @input="sendCSSThroughSocket"
                    type="text"
                    v-model="my_name" />
                  </h4>

                  <div class="clients-holder">
                    <div v-for="client in clients"
                        class="col-xs-12 col-sm-6 col-md-4 col-lg-2 client-box"
                        >
                        <div v-bind:style="client.css">
                            <span class="client-name">@{{ client.name }}</span>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </body>

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
            'ws_url'    => explode(':', str_replace('http://', '', str_replace('https://', '', App::make('url')->to('/'))))[0],
        ]); ?>
    </script>
    <script src="/js/app.js"></script>
</html>
