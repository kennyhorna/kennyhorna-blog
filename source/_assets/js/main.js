window.axios = require('axios');
window.fuse = require('fuse.js');
window.Vue = require('vue');

import Search from './components/Search.vue';
import NavigationOnPage from './components/navigation-on-page.vue';
import hljs from 'highlight.js/lib/highlight';

// Syntax highlighting
hljs.registerLanguage('bash', require('highlight.js/lib/languages/bash'));
hljs.registerLanguage('css', require('highlight.js/lib/languages/css'));
hljs.registerLanguage('html', require('highlight.js/lib/languages/xml'));
hljs.registerLanguage('javascript', require('highlight.js/lib/languages/javascript'));
hljs.registerLanguage('json', require('highlight.js/lib/languages/json'));
hljs.registerLanguage('markdown', require('highlight.js/lib/languages/markdown'));
hljs.registerLanguage('php', require('highlight.js/lib/languages/php'));
hljs.registerLanguage('scss', require('highlight.js/lib/languages/scss'));
hljs.registerLanguage('yaml', require('highlight.js/lib/languages/yaml'));

document.querySelectorAll('pre code').forEach((block) => {
  hljs.highlightBlock(block);
});

Vue.config.productionTip = false;

new Vue({
  el: '#vue-app',
  components: {
    Search, NavigationOnPage,
  },
  data() {
    return {
      pageHeadings: []
    }
  },
  mounted() {
    document.querySelectorAll('h3').forEach((heading) => {
      heading.id = heading.textContent.replace(/\s+/g, '-').toLowerCase();
      this.pageHeadings.push(heading);
    });
  },
});

