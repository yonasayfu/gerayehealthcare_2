<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Geraye Healthcare API Docs</title>
  @vite(['resources/js/swagger.ts'])
</head>
<body>
  <div id="swagger-ui"></div>
  <script>
    window.swaggerSpecUrl = @json(route('api.docs.spec'))
  </script>
</body>
<style>
  body { margin: 0; }
</style>
</html>
