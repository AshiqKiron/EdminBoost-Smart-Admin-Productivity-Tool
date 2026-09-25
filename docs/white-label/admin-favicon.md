# Admin favicon

EdminBoost can set a **custom favicon** (browser tab icon) for **wp-admin** screens. This is separate from site front-end favicons managed by your theme or SEO plugin.

## Where to configure it

The favicon is **not** on the White Label page. Set it under:

**EdminBoost → Theme** (Appearance) → **Appearance extras** → **Admin favicon ID**

- Field: `command_center.theme.admin_favicon_id`
- Input: media library **attachment ID** (integer).
- Setting help tooltip describes it as the attachment used as the favicon on wp-admin screens.

Use the Theme extras **live preview** (`#edminboost-theme-extras-preview`) to confirm the image before saving the Theme page.

See [Theme (Appearance)](../command-center/theme-appearance.md) for the full extras section (background image, font size, post status colors, etc.).

## How it is applied

`EDMINBOOST_White_Label::print_admin_favicon()` runs on `admin_head` and prints:

```html
<link rel="icon" href="…attachment url…" />
```

when `admin_favicon_id` is a valid attachment with a resolvable URL.

**Note:** The favicon hook is registered with White Label, but the **image ID comes from Theme settings**. There is no separate favicon upload on the White Label form.

### Does White Label need to be enabled?

`print_admin_favicon()` does **not** check the white-label master toggle or Pro state—it only checks that a theme favicon attachment ID is set. In practice you configure favicon while customizing admin appearance on **Theme**.

(White-label **rebranding** filters for footer, plugin row, and menu label **do** require the white-label master toggle and Pro active state.)

## Choosing an image

1. Upload a square icon (PNG, ICO, or SVG if allowed by your media settings) to the **Media Library**.
2. Note the attachment **ID** (from the media screen URL `post=123` or attachment details).
3. Enter the ID in **Admin favicon ID** on Theme.
4. **Save** the Theme (Appearance) page.

Recommended: simple square logo, roughly 32×32 to 512×512 source; browsers scale as needed.

## Relationship to Theme visual mode

Admin favicon is independent of **theme preset**, **light/dark mode**, and Menu Studio colors. It applies across wp-admin whenever the attachment ID is set.

Command Center **Theme → Admin background** and **background image** change admin styling but not the favicon field.

## Troubleshooting

| Issue | What to check |
|-------|----------------|
| Tab icon unchanged | Correct attachment ID; attachment not trashed; save Theme page; hard refresh browser cache. |
| Broken icon | Attachment must be an image MIME type with a generated URL. |
| Looking for favicon on White Label | Use **Theme → Appearance extras** instead. |
| Favicon on public site | This setting targets **wp-admin** only; set site favicon via theme/customizer or SEO plugin. |

## See also

- [White Label overview](overview.md)
- [Enable branding and plugin rebrand](enable-branding-and-plugin-rebrand.md)
- [Theme (Appearance)](../command-center/theme-appearance.md)
