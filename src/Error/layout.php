<!doctype html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Strata Error</title>
        <meta name='robots' content='noindex,follow' />
        <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
        <style type="text/css">
            body { font-family: "Open Sans", arial, sans; color:#000; padding: 1em;}
            h1, h3, h4 { margin:0; line-height:1.4; }
            h1 { font-size: 30px; }
            h3 { color:#ff0000; font-size: 25px;  }
            h4 { color:#666; font-size: 15px; margin-bottom: 1em; }

            table { color:#666; border-collapse: collapse; border:1px solid #ddd; border-bottom-width: 2px; width: 100%;}
            table td:even { background: #fefefe; }
            table td.lines { background-color: #efefef; color: #888; font-size: 14px; text-align: right; white-space: nowrap; vertical-align: middle; border-right:1px solid #ddd; width: 30px; padding:0 .8em;}
            table td.code, .code { white-space: pre-wrap; border-bottom: 1px solid #eee; padding: 0.25em;}
            table td.focus { background-color: #ffd0ce; color:#000;}
            .source { font-size: 12px; color:#888; margin: 1em 0 .25em 0; }
            .trace, .context { float:left; width:50%; margin-top: 2em; }
        </style>
    </head>
  <body class="strata-error-page">
        <?php echo Strata\View\Template::TPL_YIELD; ?>
  </body>
</html>
