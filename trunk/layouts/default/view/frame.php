<!doctype HTML>
<html>
  <head>
    <script src="<?php print $app['home'];?>xjs/jq/jquery.js" type="text/javascript"></script>
    <script type="text/javascript">
      function postHistory(){
        var target = parent.postMessage ? parent : (parent.document.postMessage ? parent.document : undefined);
        if (typeof target != "undefined"){
          parent.$(parent.document).trigger("music.history.parent", ['history', '<?php print $action;?>']);
        }
      }
      $(document).ready(function(){
        $('#history-form').bind('musichistoryframe', getCommand);
        postHistory();
      });

      function submit_form(p){
        alert('do submit:'+p);
        return true;
      }

      function getCommand(e, d1, d2){
        
        alert('called:'+d2);

        switch(d2){
          case('goforward'):
            history.forward();
            break;
          case('goback'):
            history.go(-1);
            alert('goback');
            break;
          default:
       
            $('#history-form').attr('action', d2);
            $('#history-id').val(Math.floor(Math.random()*1001));
            $('#history-form').submit();
            $('#history-form').hide();
           break;
        }
      }
      </script>
  </head>
  <body>
    <?php
      $form_action = '';
      switch($action){
        case('home'):
          $form_action = 'forward';
          break;
        case('back'):
          $form_action = 'home';
          break;
      }

    ?>
    <div id="frame-logger"></div>
    <form id="history-form" name="history-form" method="POST" action="<?php print $form_action;?>" target="_self">
      <input type="text" name="history-id" id="history-id" />
    </form>
    <?php print $action;?>
  </body>
</html>