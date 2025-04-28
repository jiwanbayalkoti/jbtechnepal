# Required Library Files

For better performance, download the following JavaScript libraries and place them in this directory:

## JavaScript Libraries:

1. **Bootstrap 5** (Required Files):
   - bootstrap.bundle.min.js
   - Download from: https://getbootstrap.com/docs/5.0/getting-started/download/

2. **Font Awesome** (Required Files):
   - all.min.js (or fontawesome.min.js)
   - Download from: https://fontawesome.com/download

3. **jQuery** (Optional, but recommended for some Bootstrap features):
   - jquery.min.js
   - Download from: https://jquery.com/download/

## Referencing in Your Layout File

Update your `layouts/app.blade.php` file to reference these local files instead of CDN links:

```html
<!-- CSS -->
<link href="{{ asset('lib/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('lib/fontawesome.min.css') }}" rel="stylesheet">

<!-- JS -->
<script src="{{ asset('lib/jquery.min.js') }}"></script>
<script src="{{ asset('lib/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('lib/all.min.js') }}"></script>

<!-- Add custom scripts section -->
@yield('scripts')
```

## Performance Benefits

- Reduced dependency on external services
- Faster page load times
- Works offline or in environments with limited connectivity
- Better control over the versions of libraries used

## Version Recommendations

- Bootstrap: 5.0.2 or newer
- Font Awesome: 5.15.4 or newer
- jQuery: 3.6.0 or newer 