from pathlib import Path

path = Path('resources/views/client_portal/request_service.blade.php')
text = path.read_text(encoding='utf-8')
needle = '</x-slot>\n\n    <div class="row">'
print('needle repr:', repr(needle))
print('slice repr:', repr(text[text.rfind('</x-slot>'):text.rfind('</x-slot>')+50]))
print('contains:', needle in text)
print('rfind idx', text.rfind(needle))
