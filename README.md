# Synaplan WP AI

A WordPress plugin that integrates Synaplan AI chat widget into your WordPress site with a wizard-style setup procedure.

## Description

Synaplan WP AI provides an easy way to add intelligent chat functionality to your WordPress website. The plugin features a step-by-step wizard that guides you through the setup process, including user registration, configuration, and widget integration.

## Features

- **Wizard-Style Setup**: Step-by-step configuration process
- **User Registration**: Automatic account creation with email confirmation
- **File Upload Support**: Upload DOCX and PDF files to enhance AI knowledge
- **Widget Customization**: Customize colors, position, and behavior
- **Mobile Optimized**: Responsive design for all devices
- **API Integration**: Secure communication with Synaplan backend
- **Open Source**: Licensed under Apache 2.0

## Installation

### From WordPress Admin

1. Go to **Plugins** → **Add New**
2. Search for "Synaplan WP AI"
3. Click **Install Now** and then **Activate**

### Manual Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/synaplan-ai-support-chat/`
3. Activate the plugin through the **Plugins** menu

## Quick Start

1. **Activate** the plugin
2. Go to **Synaplan AI** in your WordPress admin menu
3. Follow the **Setup Wizard** to configure your chat widget
4. Complete the wizard steps:
   - Enter your email and password
   - Configure basic settings
   - Upload knowledge files (optional)
   - Confirm your setup
5. Your chat widget will be automatically added to your site

## Configuration

### Basic Settings

- **Language**: Select your website's primary language
- **Intro Message**: Customize the welcome message
- **Support Prompt**: Define how the AI should respond

### Widget Appearance

- **Integration Type**: Choose between floating button or inline box
- **Colors**: Customize primary and icon colors
- **Position**: Set widget placement (bottom-right, bottom-left, bottom-center)
- **Auto-open**: Enable automatic popup after a few seconds

### Advanced Options

- **File Upload**: Add DOCX and PDF files to enhance AI knowledge
- **Custom Prompts**: Create specialized AI responses
- **Analytics**: Monitor chat usage and performance

## Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 8.0 or higher
- **Memory**: 128MB minimum
- **Storage**: 10MB for plugin files

## API Integration

The plugin integrates with the Synaplan API to provide:

- User account management
- AI chat functionality
- File processing and knowledge enhancement
- Widget configuration and customization

## Security

- All API communications use HTTPS
- API keys are encrypted and stored securely
- Input validation and sanitization
- WordPress nonce verification
- Capability-based access control

## Support

### Documentation

- [Plugin Documentation](https://docs.synaplan.com/wordpress-plugin)
- [API Reference](https://docs.synaplan.com/api)
- [Widget Examples](https://docs.synaplan.com/widget-examples)

### Getting Help

- **GitHub Issues**: [Report bugs or request features](https://github.com/synaplan/synaplan-ai-support-chat/issues)
- **WordPress Support**: [Plugin support forum](https://wordpress.org/support/plugin/synaplan-ai-support-chat)
- **Email Support**: support@synaplan.com

## Development

### Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

### Building from Source

```bash
# Clone the repository
git clone https://github.com/synaplan/synaplan-ai-support-chat.git

# Install dependencies
composer install

# Run tests
composer test

# Build for production
composer build
```

### Development Environment

1. Set up a local WordPress installation
2. Clone the plugin repository
3. Create a symlink to the plugin directory
4. Activate the plugin in WordPress admin

## Changelog

### Version 1.0.0
- Initial release
- Wizard-style setup procedure
- Basic widget integration
- File upload support
- Admin dashboard

## License

This plugin is licensed under the Apache License 2.0. See [LICENSE](LICENSE) for details.

## Credits

- **Synaplan Team**: Plugin development and maintenance
- **WordPress Community**: Platform and ecosystem
- **Contributors**: Code contributions and feedback

## Screenshots

### Setup Wizard
![Setup Wizard](assets/images/screenshots/wizard-step-1.png)

### Admin Dashboard
![Admin Dashboard](assets/images/screenshots/dashboard.png)

### Widget Preview
![Widget Preview](assets/images/screenshots/widget-preview.png)

---

**Synaplan WP AI** - Bringing intelligent chat to your WordPress site.