<?php

function icon(string $name, string $class = ''): string
{
    $paths = [
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'box' => '<path d="m12 3 9 5-9 5-9-5 9-5Zm-9 5v9l9 5 9-5V8M12 13v9M7.5 5.5l9 5"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m20 0v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/><circle cx="9" cy="7" r="4"/>',
        'staff' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M9 5V3h6v2M8 17a4 4 0 0 1 8 0"/><circle cx="12" cy="11" r="2"/>',
        'cart' => '<path d="M3 3h2l3 12h11l3-8H6M9 20h.01M18 20h.01"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>',
        'receipt' => '<path d="M5 3v18l3-2 4 2 4-2 3 2V3l-3 2-4-2-4 2-3-2ZM9 9h6M9 13h6M9 17h3"/>',
        'arrow' => '<path d="M5 12h14m-5-5 5 5-5 5"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="7.5"/><path d="m16 16 5 5"/>',
        'logout' => '<path d="M9 4H4v16h5m0-8h12m-4-4 4 4-4 4"/>',
        'edit' => '<path d="m16 3 5 5-12 12H4v-5L16 3Zm-3 3 5 5"/>',
        'trash' => '<path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7"/>',
        'chevron' => '<path d="m9 5 7 7-7 7"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'alert' => '<path d="m12 3 10 18H2L12 3Zm0 6v5m0 3h.01"/>',
        'upload' => '<path d="M12 16V3m-5 5 5-5 5 5M3 16v5h18v-5"/>',
        'lock' => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V6a4 4 0 1 1 8 0v4m-4 5v2"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M7 3v4m10-4v4M7 14h3m4 0h3m-10 3h3"/>',
        'trend' => '<path d="m3 17 6-6 4 4L21 5m-7 0h7v7"/>',
        'wallet' => '<path d="M21 8H5a2 2 0 0 1 0-4h13v4M3 6v13a2 2 0 0 0 2 2h16V8m0 4h-6v5h6m-3-2.5h.01"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'minus' => '<path d="M5 12h14"/>',
        'leaf' => '<path d="M20 4C10 2 2 7 5 16c9 3 16-3 15-12ZM4 21 15 10"/>',
    ];
    return '<svg class="icon ' . esc($class, 'attr') . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? $paths['box']) . '</svg>';
}

function money($value): string
{
    return '₱' . number_format((float) $value, 2);
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/u', trim($name));
    return mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1) . (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : ''));
}

function image_url(?string $name): string
{
    if ($name && preg_match('/^sample-(shirt|hoodie|tote|notebook|tumbler|lanyard|cap|pen)\.svg$/D', $name)) {
        return base_url('assets/products/' . $name);
    }
    return $name && preg_match('/^[a-f0-9]{40}\.jpg$/D', $name)
        ? site_url('media/' . $name) : base_url('assets/products/placeholder.svg');
}

function form_value(string $field, array $row = [], string $default = ''): string
{
    $data = session()->getFlashdata('form_data') ?? [];
    return esc((string) ($data[$field] ?? $row[$field] ?? $default), 'attr');
}

