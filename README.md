# Synaplan AI Support Chat Wordpress Plugin

- Contributors: synaplan, metadist_synaplan, metadist
- Tags: ai, chat, chatbot, support, customer-service
- Requires at least: 5.0
- Tested up to: 6.8
- Requires PHP: 7.3
- Stable tag: 1.0.10
- License: Apache-2.0
- License URI: https://www.apache.org/licenses/LICENSE-2.0

## Synaplan AI Support Chat

A WordPress plugin that integrates Synaplan AI support chat widget into your WordPress site with a wizard-style setup.

## Description

Synaplan AI Support Chat provides an easy way to add intelligent chat functionality to your WordPress website. The plugin features a step-by-step wizard that guides you through the setup process, including user registration, configuration, and widget integration.

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
- **PHP**: 7.3 or higher (compatible with PHP 7.3, 7.4, 8.0, 8.1, 8.2, 8.3+)
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

- [API Documentation](https://docs.synaplan.com/)
- [Widget Examples](https://docs.synaplan.com/widget-inline-demo.php)
- [Synaplan Dashboard](https://app.synaplan.com/)
- [API Documentation](https://docs.synaplan.com/)
- [Widget Examples](https://docs.synaplan.com/widget-inline-demo.php)
- [Synaplan Dashboard](https://app.synaplan.com/)

### Getting Help

- **GitHub Issues**: [Report bugs or request features](https://github.com/metadist/synaplan-ai-support-chat/issues)
- **GitHub Issues**: [Report bugs or request features](https://github.com/metadist/synaplan-ai-support-chat/issues)
- **WordPress Support**: [Plugin support forum](https://wordpress.org/support/plugin/synaplan-ai-support-chat)
- **Email Support**: support@synaplan.com

## Development

### Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

### Building from Source

```bash
# Clone the repository
git clone https://github.com/metadist/synaplan-ai-support-chat.git
git clone https://github.com/metadist/synaplan-ai-support-chat.git

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

### Version 1.0.5
- **Metadata Corrections**: Updated author attribution to metadist GmbH
- **URL Corrections**: Fixed GitHub repository URLs to metadist organization
- **Documentation Links**: Updated to correct documentation URLs at docs.synaplan.com
- No functional changes - plugin works identically

### Version 1.0.5
- **Metadata Corrections**: Updated author attribution to metadist GmbH
- **URL Corrections**: Fixed GitHub repository URLs to metadist organization
- **Documentation Links**: Updated to correct documentation URLs at docs.synaplan.com
- No functional changes - plugin works identically

### Version 1.0.4
- **PHP Compatibility**: Now supports PHP 7.3, 7.4, and 8.x
- **Graceful Degradation**: Added runtime PHP version check with clear error messages
- **Broader Compatibility**: Enables installation on more hosting environments
- Improved version detection and plugin activation handling

### Version 1.0.3
- Complete wizard flow with site verification
- Automatic user creation with instant activation
- API key generation and management
- Knowledge base file upload and vectorization
- Prompt configuration with file search
- Widget configuration saved to database
- Click-to-reveal API key in dashboard
- Full WordPress.org compliance
- Security improvements and proper escaping
- Plugin renamed to "Synaplan AI Support Chat"

### Version 1.0.0
- Initial release
- Wizard-style setup procedure
- Basic widget integration
- File upload support
- Admin dashboard

## License

This plugin is licensed under the Apache License 2.0. See [LICENSE](LICENSE) for details.

## Credits

- **metadist GmbH**: Plugin development and maintenance
- **Synaplan**: AI platform and API
- **metadist GmbH**: Plugin development and maintenance
- **Synaplan**: AI platform and API
- **WordPress Community**: Platform and ecosystem
- **Contributors**: Code contributions and feedback

## Screenshots

### Admin Dashboard
![Admin Dashboard](https://cdn.prod.website-files.com/68c965a57724e7f0a00435b9/68e805e0e81acbac724386a0_wordpress-screen.png)

### Widget Preview
Multi-lingual chat widget with your data:
![Widget Preview](https://cdn.prod.website-files.com/68c965a57724e7f0a00435b9/68e8106973141c9e57b5fd5f_chat-support.png)

---

**Synaplan WP AI** - Bringing intelligent chat to your WordPress site.
