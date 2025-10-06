# Git Management Tools for Geraye Healthcare

This directory contains tools to help manage both the web and mobile repositories for the Geraye Healthcare project.

## 📁 Directory Structure

```
/Users/yonassayfu/VSProject/gerayehealthcare/
├── GIT_MANAGEMENT_GUIDE.md     # Comprehensive Git management guide
├── git-sync-both.sh           # Automated script for both repositories
├── GIT_TOOLS_README.md        # This file
└── gerayehealthcare-mobile-app/  # Mobile application repository
```

## 📖 Documentation

### GIT_MANAGEMENT_GUIDE.md
A comprehensive guide that explains:
- Repository structure and information
- Current status of both repositories
- Recommended Git workflow
- Best practices for commit messages and branch naming
- Useful Git commands
- Next steps recommendation

## ⚙️ Automation Script

### git-sync-both.sh
An automated script to manage both repositories simultaneously.

#### Usage:
```bash
# Make the script executable (already done)
chmod +x git-sync-both.sh

# Show status of both repositories
./git-sync-both.sh status

# Commit changes in both repositories
./git-sync-both.sh commit "Your commit message"

# Push changes to both repositories
./git-sync-both.sh push

# Pull latest changes from both repositories
./git-sync-both.sh pull

# Create backup tags for both repositories
./git-sync-both.sh backup

# Full sync (pull -> commit -> push)
./git-sync-both.sh sync
```

#### Examples:
```bash
# Check the status of both repositories
./git-sync-both.sh status

# Commit all changes with a descriptive message
./git-sync-both.sh commit "feat: implement mobile API enhancements and sync features"

# Push all changes to remote repositories
./git-sync-both.sh push

# Create backup tags before major changes
./git-sync-both.sh backup

# Perform a full synchronization
./git-sync-both.sh sync
```

## 🚀 Quick Start

1. **Check current status**:
   ```bash
   ./git-sync-both.sh status
   ```

2. **Commit your changes**:
   ```bash
   ./git-sync-both.sh commit "Add new healthcare features and API endpoints"
   ```

3. **Push to remote repositories**:
   ```bash
   ./git-sync-both.sh push
   ```

## 📝 Best Practices

1. **Always check status first**:
   ```bash
   ./git-sync-both.sh status
   ```

2. **Create backups before major changes**:
   ```bash
   ./git-sync-both.sh backup
   ```

3. **Use descriptive commit messages**:
   - Start with a verb (add, fix, update, etc.)
   - Be specific about what changed
   - Keep messages concise but informative

4. **Regular synchronization**:
   ```bash
   ./git-sync-both.sh sync
   ```

## 🆘 Troubleshooting

### Common Issues:

1. **Permission denied**:
   ```bash
   chmod +x git-sync-both.sh
   ```

2. **Repository not found**:
   - Ensure both repositories exist in the expected locations
   - Check the paths in the script if they've changed

3. **Git errors**:
   - Check your internet connection
   - Verify you have proper permissions for the repositories
   - Ensure you've authenticated with GitHub

### Getting Help:
```bash
./git-sync-both.sh
```
Running the script without arguments will display usage information.

## 📞 Support

For issues with these tools, please contact the development team or check the documentation in:
- `GIT_MANAGEMENT_GUIDE.md`
- Individual repository README files

---

**Last Updated**: October 6, 2025