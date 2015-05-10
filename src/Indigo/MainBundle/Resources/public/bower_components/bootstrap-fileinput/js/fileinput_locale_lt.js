/*!
 * FileInput <_LANG_> Translations
 *
 * This file must be loaded after 'fileinput.js'. Patterns in braces '{}', or
 * any HTML markup tags in the messages must not be converted or translated.
 *
 * @see http://github.com/kartik-v/bootstrap-fileinput
 *
 * NOTE: this file must be saved in UTF-8 encoding.
 */
(function ($) {
    "use strict";

    $.fn.fileinput.locales._lt = {
        fileSingle: 'failas',
        filePlural: 'failai',
        browseLabel: ' Pasirinkite failą',
        removeLabel: ' Pašalinti',
        removeTitle: 'Ištrinti pasirinktus failus',
        cancelLabel: 'Atšaukti',
        cancelTitle: 'Atšaukti dabartinį kėlimą',
        uploadLabel: 'Įkelti',
        uploadTitle: 'Įkelti pasirinktus failus',
        msgSizeTooLarge: 'Failas "{name}" (<b>{size} KB</b>) viršija leistiną failo kėlimo dydį <b>{maxSize} KB</b>. Prašome kartoti failo įkėlima su mažesniu dydžiu!',
        msgFilesTooLess: 'Jūs privalot pasirinkti mažiausiai <b>{n}</b> {files} įkėlimui. Prašome bandyti iš naujo!',
        msgFilesTooMany: 'Pasirinktų failų skaičius <b>({n})</b> viršiją leistiną limitą <b>{m}</b>. Prašome bandyti iš naujo!',
        msgFileNotFound: 'Failas "{name}" nerastas!',
        msgFileSecured: 'Saugumo apribojimai neleidžia skaityti failo "{name}".',
        msgFileNotReadable: 'Failas "{name}" neskaitomas.',
        msgFilePreviewAborted: 'Failo peržiūra atšaukta "{name}".',
        msgFilePreviewError: 'Skaitant failą įvyko klaida "{name}".',
        msgInvalidFileType: 'Blogas failo formatas "{name}". Tik "{types}" failų tipai yra palaikomi.',
        msgInvalidFileExtension: 'Blogi failų formatai "{name}". Tik "{extensions}" failų formatai yra palaikomi.',
        msgValidationError: 'Failo įkėlimo klaida',
        msgLoading: 'Kraunamas failas {index} iš {files} &hellip;',
        msgProgress: 'Kraunamas {index} iš {files} - {name} - {percent}% baigta.',
        msgSelected: '{n} failai pasirinkti',
        msgFoldersNotAllowed: 'Tik traukite ir paleiskite failus! Praleista {n} paleistų aplankų.',
        dropZoneTitle: 'Traukite ir paleiskite failus čia &hellip;'
    };

    $.extend($.fn.fileinput.defaults, $.fn.fileinput.locales._lt);
})(window.jQuery);