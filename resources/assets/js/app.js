
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

const app = new Vue({
    el: '#app',

    data: {

        editor_toggled: true,
        conn: new WebSocket('ws://'+window.Laravel.ws_url+':9090'),

        my_id: null,
        my_name: 'John Snow',

        options: {
          style: 'linear',
          direction: 'top',
          start_color: '#74e3ec',
          end_color: '#c7ffe2',
        },

        clients: {
        },
    },

    methods: {
        setOption: function (option, value) {
          this.options[option] = value;
          this.sendCSSThroughSocket();
        },

        hasOptionAs: function (option, value) {
          return this.options[option] == value;
        },

        selectText: function (evt) {
          if (document.selection) {
              var range = document.body.createTextRange();
              range.moveToElementText(evt.srcElement);
              range.select();
          } else if (window.getSelection) {
              var range = document.createRange();
              range.selectNode(evt.srcElement);
              window.getSelection().addRange(range);
          }
        },

        sendCSSThroughSocket: function() {
            this.conn.send(JSON.stringify({
                'client_id': this.my_id,
                'name': this.my_name,
                'type': 'css_update',
                'css': this.cssObject
            }))
        },
    },

    computed: {
        css: function () {
          var css = 'background: ' + this.options.start_color + ' /* Old browsers */;\n';
          
          var browsers = [
            { name: 'Safari 5.1-6', key: '-webkit-' },
            { name: 'Opera 11.1-12', key: '-o-' },
            { name: 'Fx 3.6-15', key: '-moz-' },
            { name: 'Standard', key: '' }
          ];
          for (var i in browsers) {
            var browser = browsers[i];
            var direction = this.options.direction;
            if (browser.name == 'Standard') {
              if (direction == 'bottom') {
                direction = 'to top';
              }
              if (direction == 'top') {
                direction = 'to bottom';
              }
              if (direction == 'right') {
                direction = 'to left';
              }
              if (direction == 'left') {
                direction = 'to right';
              }
            }
            css += 'background: '
                  + browser.key
                  + this.options.style
                  + '-gradient('
                  + direction + ','
                  + this.options.start_color + ','
                  + this.options.end_color
                  + '); /* '+ browser.name +' */ \n';
          }
          return css;
        },

        cssHTML: function () {
          return this.css.replace(/(?:\r\n|\r|\n)/g, '<br />');
        },

        cssObject: function (){
          return {
            'background' : '-webkit-'
                  + this.options.style
                  + '-gradient('
                  + this.options.direction + ','
                  + this.options.start_color + ','
                  + this.options.end_color + ')'
          }
        }
    },

    mounted: function() {
        this.conn.onclose = function (event) {
            var reason;
            if (event.code == 1000)
                reason = "Normal closure, meaning that the purpose for which the connection was established has been fulfilled.";
            
            else if(event.code == 1001)
                reason = "An endpoint is \"going away\", such as a server going down or a browser having navigated away from a page.";
            
            else if(event.code == 1002)
                reason = "An endpoint is terminating the connection due to a protocol error";
            
            else if(event.code == 1003)
                reason = "An endpoint is terminating the connection because it has received a type of data it cannot accept (e.g., an endpoint that understands only text data MAY send this if it receives a binary message).";
            
            else if(event.code == 1004)
                reason = "Reserved. The specific meaning might be defined in the future.";
            
            else if(event.code == 1005)
                reason = "No status code was actually present.";
            
            else if(event.code == 1006)
               reason = "Abnormal error, e.g., without sending or receiving a Close control frame";
            
            else if(event.code == 1007)
                reason = "An endpoint is terminating the connection because it has received data within a message that was not consistent with the type of the message (e.g., non-UTF-8 [http://tools.ietf.org/html/rfc3629] data within a text message).";
            
            else if(event.code == 1008)
                reason = "An endpoint is terminating the connection because it has received a message that \"violates its policy\". This reason is given either if there is no other sutible reason, or if there is a need to hide specific details about the policy.";
            
            else if(event.code == 1009)
               reason = "An endpoint is terminating the connection because it has received a message that is too big for it to process.";
            
            else if(event.code == 1010) // Note that this status code is not used by the server, because it can fail the WebSocket handshake instead.
                reason = "An endpoint (client) is terminating the connection because it has expected the server to negotiate one or more extension, but the server didn't return them in the response message of the WebSocket handshake. <br /> Specifically, the extensions that are needed are: " + event.reason;
            
            else if(event.code == 1011)
                reason = "A server is terminating the connection because it encountered an unexpected condition that prevented it from fulfilling the request.";
            
            else if(event.code == 1015)
                reason = "The connection was closed due to a failure to perform a TLS handshake (e.g., the server certificate can't be verified).";
            else
                reason = "Unknown reason";

            alert(reason);
        }

        this.conn.onopen = function(event) {
            this.my_id = event.data;
        }.bind(this);

        this.conn.onmessage = function(event) {
            var message = JSON.parse(event.data);

            if (message.type == "new_conn") {
                this.clients[message.client_id] = {
                    name: 'Anonymous',
                    css: { 'background' : 'red' }
                };
                // notify the new client of the css
                this.sendCSSThroughSocket();
            }

            if (message.type == "conn_established") {
                this.my_id = message.client_id;
                this.my_name = this.my_name + ' ' + this.my_id;
                this.$forceUpdate();
                this.sendCSSThroughSocket();
            }

            if (message.type == "css_update") {
                if (!this.clients[message.client_id]) {
                    this.clients[message.client_id] = {
                        name: 'Anonymous',
                        css: { 'background' : 'red' }
                    };
                }
                this.clients[message.client_id].css = message.css;
                this.clients[message.client_id].name = message.name;
            }

            if (message.type == "disconnect") {
                delete this.clients[message.client_id];
            }

            this.$forceUpdate();
        }.bind(this);

    }
});
