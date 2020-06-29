## 1.0.0

- No longer uses `save_post` and `transition_post_status` to detect updates. Only uses `transition_post_status`
- Fix undefined offset error when options not yet saved

## 0.4.1

- No longer uses `sanitize_text_field()` on webhook url

## 0.4.0

- Add support for `transition_post_status` to better handle post changes
