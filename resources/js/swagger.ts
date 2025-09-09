import 'swagger-ui-dist/swagger-ui.css'
import SwaggerUI from 'swagger-ui-dist'

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('swagger-ui')
  if (!el) return

  // @ts-ignore: route() is provided by Ziggy in Blade when we render this page
  const specUrl = (window as any).swaggerSpecUrl || '/api-docs/spec'

  SwaggerUI({
    url: specUrl,
    dom_id: '#swagger-ui',
    presets: [SwaggerUI.presets.apis],
    layout: 'BaseLayout',
  })
})

