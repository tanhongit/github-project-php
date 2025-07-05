# github-project-php 👋

<p align="center">
<a href="#"><img src="https://img.shields.io/github/license/cslant/github-project-php.svg?style=flat-square" alt="License"></a>
<a href="https://github.com/cslant/github-project-php/releases"><img src="https://img.shields.io/github/release/cslant/github-project-php.svg?style=flat-square" alt="Latest Version"></a>
<a href="https://packagist.org/packages/cslant/github-project-php"><img src="https://img.shields.io/packagist/dt/cslant/github-project-php.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://github.com/cslant/github-project-php/actions/workflows/setup_test.yml"><img src="https://img.shields.io/github/actions/workflow/status/cslant/github-project-php/setup_test.yml?label=tests&branch=main" alt="Test Status"></a>
<a href="https://github.com/cslant/github-project-php/actions/workflows/php-cs-fixer.yml"><img src="https://img.shields.io/github/actions/workflow/status/cslant/github-project-php/php-cs-fixer.yml?label=code%20style&branch=main" alt="Code Style Status"></a>

## 📝 Introduction

GitHub Project PHP is a package that helps you manage your Github projects in PHP.

It provides a simple and easy-to-use webhooks system to get the GitHub project's actions and implement comments on all activities in the project.

## Available Field Type Templates

### Standard Field Types

1. **text** - For simple text fields
2. **number** - For numeric fields
3. **date** - For date fields (formatted as Y-m-d)
4. **single_select** - For single-select dropdowns with color support
5. **multi_select** - For multi-select fields
6. **checkbox** - For boolean/toggle fields
7. **textarea** - For long text content with diff view
8. **iteration** - For iteration/sprint fields
9. **labels** - For label/tag fields
10. **assignees** - For user assignment fields
11. **milestone** - For milestone tracking
12. **unsupported** - Fallback for unknown field types

## 📋 Requirements

- PHP ^8.3
- [Composer](https://getcomposer.org/)
- [Laravel](https://laravel.com/) ^10.0

## 🔧 Installation

You can install this package via Composer:

```bash
composer require cslant/github-project-php
```

### Customizing Templates

You can publish and customize the templates by running:

```bash
php artisan vendor:publish --tag=github-project-views
```

This will copy the templates to `resources/views/vendor/github-project/md/field_types/` where you can modify them.

## 🚀 Usage

See the [Usage - GitHub Project PHP Documentation](https://docs.cslant.com/github-project-php/usage)
for a list of usage.

Please check and update some configurations in the documentation.

### Template Variables

All field type templates receive the following variables:

- `$fieldName` - The display name of the field
- `$fieldType` - The type of the field (e.g., 'text', 'number')
- `$fromValue` - The previous value of the field
- `$toValue` - The new value of the field
- `$fieldData` - Raw field data from the webhook

### Adding Custom Field Types

To add support for a custom field type:

1. Create a new template file in the `field_types` directory
2. Name it with your field type (e.g., `custom_type.blade.php`)
3. The template will automatically be used when a field of that type is encountered

### Styling

GitHub Flavored Markdown (GFM) is supported. You can use:

- `**bold**` for bold text
- `*italic*` for italic text
- `` `code` `` for inline code
- ```code blocks``` for multi-line code
- [links](https://example.com) for URLs
- HTML is also supported for more complex formatting

### Best Practices

1. Keep messages concise but informative
2. Use consistent formatting
3. Include relevant context
4. Handle null/empty values gracefully
5. Use emoji sparingly for visual cues

### Example Custom Template

Here's an example of a custom field type template:

```blade
@if($fromValue != null && $toValue != null)
    **`{{ $fieldName }}`** changed from **`{{ $fromValue }}`** to **`{{ $toValue }}`**
@elseif($toValue)
    **`{{ $fieldName }}`** set to **`{{ $toValue }}`**
@else
    **`{{ $fieldName }}`** cleared
@endif
```

## 📖 Official Documentation

Please see the [GitHub Project PHP Documentation](https://docs.cslant.com/github-project-php) for more
information.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
