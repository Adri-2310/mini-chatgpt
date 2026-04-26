import MarkdownIt from 'markdown-it';
import hljs from 'highlight.js';

let mdInstance = null;

export const useMarkdown = () => {
    if (!mdInstance) {
        mdInstance = new MarkdownIt({
            highlight: (code, lang) => {
                if (lang && hljs.getLanguage(lang)) {
                    try {
                        return `<pre class="hljs"><code>${hljs.highlight(code, { language: lang, ignoreIllegals: true }).value}</code></pre>`;
                    } catch (e) {
                        console.error('Highlight error:', e);
                    }
                }
                return `<pre class="hljs"><code>${mdInstance.utils.escapeHtml(code)}</code></pre>`;
            },
        });
    }

    const render = (content) => {
        if (!content) return '';
        return mdInstance.render(content);
    };

    return { render };
};
