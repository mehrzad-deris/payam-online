(function () {
    if (typeof window.tinymce === 'undefined') {
        return;
    }

    const cleanAttribute = (value) =>
        String(value || '')
            .replace(/[\r\n]+/g, ' ')
            .replace(/"/g, '%22')
            .trim();

    window.tinymce.PluginManager.add('payam_cta', function (editor) {
        editor.addButton('payam_cta', {
            text: 'ساخت CTA',
            icon: false,
            tooltip: 'افزودن دکمه CTA',
            onclick: function () {
                editor.windowManager.open({
                    title: 'ساخت دکمه CTA',
                    minWidth: 420,
                    body: [
                        {
                            type: 'textbox',
                            name: 'text',
                            label: 'متن دکمه',
                        },
                        {
                            type: 'textbox',
                            name: 'url',
                            label: 'آدرس لینک',
                        },
                        {
                            type: 'listbox',
                            name: 'type',
                            label: 'نوع دکمه',
                            values: [
                                { text: 'Primary', value: 'primary' },
                                { text: 'Secondary', value: 'secondary' },
                            ],
                            value: 'primary',
                        },
                        {
                            type: 'checkbox',
                            name: 'icon',
                            label: 'نمایش آیکون فلش',
                            checked: true,
                        },
                    ],
                    onsubmit: function (event) {
                        const text = String(event.data.text || '').trim();
                        const url = cleanAttribute(event.data.url);

                        if (!text || !url) {
                            event.preventDefault();
                            editor.windowManager.alert(
                                'متن دکمه و آدرس لینک الزامی هستند.'
                            );
                            return;
                        }

                        const type =
                            event.data.type === 'secondary'
                                ? 'secondary'
                                : 'primary';
                        const icon = event.data.icon ? 'yes' : 'no';
                        const safeText = text
                            .replace(/\[/g, '&#91;')
                            .replace(/\]/g, '&#93;');

                        editor.insertContent(
                            `[payam_cta type="${type}" icon="${icon}" url="${url}"]${safeText}[/payam_cta]`
                        );
                    },
                });
            },
        });
    });
})();
