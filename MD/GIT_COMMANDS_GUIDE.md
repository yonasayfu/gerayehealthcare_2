# Git Commands Guide for Laravel Boilerplate Project

This guide provides step-by-step instructions for Git operations in your Laravel boilerplate project, including configuration for multiple GitHub accounts.

**See also**: [GIT_WORKFLOW.md](GIT_WORKFLOW.md) for detailed workflow guidelines and best practices.

## Table of Contents
1. [Initial Git Configuration](#initial-git-configuration)
2. [Project Initialization](#project-initialization)
3. [Working with Remotes](#working-with-remotes)
4. [Basic Git Workflow](#basic-git-workflow)
5. [Branch Management](#branch-management)
6. [Commit Management](#commit-management)
7. [Merging and Conflict Resolution](#merging-and-conflict-resolution)
8. [Working with Multiple GitHub Accounts](#working-with-multiple-github-accounts)
9. [Sharing Project and Setting Permissions](#sharing-project-and-setting-permissions)
10. [Cleanup and Maintenance](#cleanup-and-maintenance)

## Initial Git Configuration

Configure your Git user information:

```bash
# Set global user name and email
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# Set default editor (optional)
git config --global core.editor "code -w"

# Set default branch name (optional)
git config --global init.defaultBranch main

# List all configurations
git config --list
```

For project-specific configuration (run inside project directory):

```bash
# Set project-specific user name and email for your primary account
git config user.name "yonasayfu"
git config user.email "yonasayfu28@gmail.com"
```

## Project Initialization

Initialize a new Git repository:

```bash
# Navigate to your project directory
cd /Users/yonassayfu/VSProject/BaseBoilerPlate/laravelBoilerPlate

# Initialize Git repository (if not already done)
git init

# Check status
git status
```

## Working with Remotes

**IMPORTANT**: You must create repositories manually on GitHub first before adding them as remotes. Git does not automatically create repositories on remote servers.

Steps to create repositories on GitHub:
1. Go to https://github.com
2. Log in to each account (yonasayfu, Elefensh-Yona, guangutsemera)
3. Create a new repository with the same name: `MyLaravelBoilerPlate`
4. Do NOT initialize with README, .gitignore, or license
5. Make the repository public or private as desired

After creating the repositories, add remotes for your multiple GitHub accounts:

```bash
# Add the primary remote (yonasayfu account) - SEMERA REPO
git remote add origin https://github.com/yonasayfu/MyLaravelBoilerPlate.git

# Add additional remotes for collaboration (repositories must exist first)
git remote add elefensh-yona https://github.com/Elefensh-Yona/MyLaravelBoilerPlate.git
git remote add guangut https://github.com/guangutsemera/MyLaravelBoilerPlate.git

# List all remotes
git remote -v

# Rename a remote
git remote rename old-name new-name

# Remove a remote
git remote remove remote-name

# Update remote URL
git remote set-url origin https://github.com/yonasayfu/MyLaravelBoilerPlate.git
```

## Basic Git Workflow

Daily workflow commands:

```bash
# Check status
git status

# Add files to staging
git add filename.txt          # Add specific file
git add .                     # Add all changes
git add *.php                 # Add all PHP files
git add folder/               # Add all files in folder

# Commit changes
git commit -m "Descriptive commit message"

# Push to remote
git push origin main          # Push to main branch
git push origin branch-name   # Push to specific branch

# Pull changes
git pull origin main          # Pull from main branch
git pull origin branch-name   # Pull from specific branch

# View commit history
git log
git log --oneline
git log --graph --oneline --all
```

## Branch Management

Create and manage branches:

```bash
# List branches
git branch                    # List local branches
git branch -r                 # List remote branches
git branch -a                 # List all branches

# Create a new branch
git branch feature/new-module

# Switch to a branch
git checkout feature/new-module

# Create and switch to a new branch (in one command)
git checkout -b feature/new-module

# With newer Git versions:
git switch feature/new-module
git switch -c feature/new-module

# Push a new branch to remote
git push -u origin feature/new-module

# Delete a branch
git branch -d feature/new-module      # Delete local branch
git push origin --delete feature/new-module  # Delete remote branch
```

## Commit Management

Manage commits and history:

```bash
# View commit history
git log
git log --oneline
git log --graph --oneline --all

# View specific commits
git show commit-hash
git show HEAD~1              # Show previous commit

# Amend last commit
git commit --amend -m "Updated commit message"

# Reset commits
git reset --soft HEAD~1      # Undo last commit, keep changes staged
git reset --mixed HEAD~1     # Undo last commit, unstage changes
git reset --hard HEAD~1      # Undo last commit, discard changes

# Revert a commit (create new commit that undoes changes)
git revert commit-hash
```

## Merging and Conflict Resolution

Merge branches and resolve conflicts:

```bash
# Merge a branch into current branch
git merge feature/new-module

# Merge with no fast-forward (preserves branch structure)
git merge --no-ff feature/new-module

# Abort a merge in progress
git merge --abort

# Resolve conflicts manually, then:
git add resolved-file.txt
git commit -m "Resolve merge conflicts"
```

## Working with Multiple GitHub Accounts

Configure SSH keys for multiple accounts (recommended approach):

1. Generate SSH keys for each account:
```bash
# Generate key for yonasayfu account (primary)
ssh-keygen -t rsa -b 4096 -C "yonasayfu28@gmail.com" -f ~/.ssh/id_rsa_yonasayfu

# Generate key for Elefensh-Yona account
ssh-keygen -t rsa -b 4096 -C "elefenshyona@gmail.com" -f ~/.ssh/id_rsa_elefensh

# Generate key for Guangutsemera account
ssh-keygen -t rsa -b 4096 -C "guangutsemera@gmail.com" -f ~/.ssh/id_rsa_guangut
```

2. Add keys to SSH agent:
```bash
ssh-add ~/.ssh/id_rsa_yonasayfu
ssh-add ~/.ssh/id_rsa_elefensh
ssh-add ~/.ssh/id_rsa_guangut
```

3. Create/edit SSH config file:
```bash
nano ~/.ssh/config
```

Add the following content:
```
# yonasayfu account (primary) - SEMERA
Host github-yonasayfu
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_rsa_yonasayfu

# Elefensh-Yona account
Host github-elefensh
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_rsa_elefensh

# Guangutsemera account
Host github-guangut
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_rsa_guangut
```

4. Update remote URLs to use SSH hosts:
```bash
# Change HTTPS remotes to SSH with custom hosts
git remote set-url origin git@github-yonasayfu:yonasayfu/MyLaravelBoilerPlate.git
git remote set-url elefensh-yona git@github-elefensh:Elefensh-Yona/MyLaravelBoilerPlate.git
git remote set-url guangut git@github-guangut:guangutsemera/MyLaravelBoilerPlate.git
```

Alternative approach using HTTPS with credentials:

```bash
# Configure Git credentials helper
git config --global credential.helper store

# For each repository, Git will prompt for credentials and store them
git push origin main
# Enter username and personal access token when prompted
```

## Sharing Project and Setting Permissions

To share your project with collaborators and set different permission levels:

### 1. Repository Access Levels on GitHub

GitHub offers two main permission levels for collaborators:
- **Admin/Owner**: Full access to the repository, including deletion and permission management
- **Write/Contributor**: Can push code, create branches, and create pull requests
- **Read**: Can only view and clone the repository

### 2. Adding Collaborators with Different Permissions

**For GitHub (using web interface)**:

1. Go to your repository on GitHub (https://github.com/yonasayfu/MyLaravelBoilerPlate)
2. Click on "Settings" tab
3. Click on "Collaborators & teams" in the left sidebar
4. Click "Add people"
5. Enter the GitHub username or email of the person you want to invite
6. Select the appropriate permission level:
   - For full access: Select "Admin"
   - For contribution access: Select "Write"
7. Click "Add collaborator"

**For multiple collaborators with different permissions**:
- Invite the person who needs full access (admin permissions) with "Admin" role
- Invite the person who should only contribute with "Write" role

### 3. Collaborator Workflow

Once collaborators are added, they can:

**For the person with full access (Admin)**:
```bash
# Clone the repository
git clone https://github.com/yonasayfu/MyLaravelBoilerPlate.git

# Configure their Git user info
git config user.name "Their Name"
git config user.email "their.email@example.com"

# Work on features
git checkout -b feature/new-feature
git add .
git commit -m "Add new feature"
git push origin feature/new-feature

# Merge branches (has permission to merge directly)
git checkout main
git pull origin main
git merge feature/new-feature
git push origin main
```

**For the person with contribution access (Write)**:
```bash
# Clone the repository
git clone https://github.com/yonasayfu/MyLaravelBoilerPlate.git

# Configure their Git user info
git config user.name "Their Name"
git config user.email "their.email@example.com"

# Work on features
git checkout -b feature/new-feature
git add .
git commit -m "Add new feature"
git push origin feature/new-feature

# Create pull request (must go through review process)
# They can push branches but typically need approval to merge to main
```

### 4. Best Practices for Collaborative Development

1. **Use Pull Requests for Code Review**:
   - Even admins should use pull requests for significant changes
   - This ensures code quality and knowledge sharing

2. **Branch Protection Rules**:
   - Set up branch protection on main branch
   - Require pull request reviews before merging
   - Require status checks to pass before merging

3. **Communication Guidelines**:
   - Use descriptive commit messages
   - Keep pull requests focused on single features
   - Comment on pull requests for discussion

## Cleanup and Maintenance

Clean up and maintain your repository:

```bash
# Remove untracked files
git clean -n             # Dry run (see what would be removed)
git clean -f             # Remove untracked files
git clean -fd            # Remove untracked files and directories

# Unstage files
git reset HEAD filename.txt     # Unstage specific file
git reset HEAD .                # Unstage all files

# Discard changes in working directory
git checkout -- filename.txt    # Discard changes in specific file
git checkout -- .               # Discard all changes

# Garbage collection
git gc                          # Clean up unnecessary files and optimize local repository

# Prune remote-tracking branches
git remote prune origin         # Remove deleted remote branches from local
```

## Best Practices for This Project

1. **Branch Naming Convention**:
   - Features: `feature/module-name`
   - Bug fixes: `fix/issue-description`
   - Releases: `release/version-number`

2. **Commit Message Guidelines**:
   - Use present tense ("Add feature" not "Added feature")
   - Capitalize first letter
   - Limit first line to 50 characters
   - Use body for detailed explanations when needed

3. **Workflow for New Modules**:
   ```bash
   # 1. Create new branch for module
   git checkout -b feature/user-management
   
   # 2. Work on module (frequent commits)
   git add .
   git commit -m "Add user model and migration"
   
   # 3. Push branch to remote
   git push -u origin feature/user-management
   
   # 4. After 10 code changes, commit
   git add .
   git commit -m "Implement user CRUD operations"
   git push origin feature/user-management
   
   # 5. When module is complete, merge to main
   git checkout main
   git pull origin main
   git merge feature/user-management
   git push origin main
   
   # 6. Delete feature branch
   git branch -d feature/user-management
   git push origin --delete feature/user-management
   ```

4. **Collaboration Workflow**:
   - Always pull latest changes before starting work
   - Create feature branches for all new work
   - Push feature branches regularly for backup
   - Create pull requests for code review before merging
   - Delete merged branches to keep repository clean

5. **Specific Commands for Your Setup**:
   ```bash
   # Initialize repository (if needed)
   git init
   
   # Configure user info
   git config user.name "yonasayfu"
   git config user.email "yonasayfu28@gmail.com"
   
   # Add primary remote (SEAMERA) - REPOSITORY MUST EXIST FIRST ON GITHUB
   git remote add origin https://github.com/yonasayfu/MyLaravelBoilerPlate.git
   
   # Add collaboration remotes - REPOSITORIES MUST EXIST FIRST ON GITHUB
   git remote add elefensh-yona https://github.com/Elefensh-Yona/MyLaravelBoilerPlate.git
   git remote add guangut https://github.com/guangutsemera/MyLaravelBoilerPlate.git
   
   # Verify remotes
   git remote -v
   
   # Stage and commit initial files
   git add .
   git commit -m "Initial commit: Laravel Boilerplate setup"
   
   # Push to primary repository
   git push -u origin main
   
   # Create feature branch for new work
   git checkout -b feature/staff-management
   
   # Work on feature...
   
   # Commit changes (every 10 changes)
   git add .
   git commit -m "Implement staff model and controller"
   
   # Push feature branch
   git push -u origin feature/staff-management
   ```

This guide should help you efficiently manage your Laravel boilerplate project with Git across multiple GitHub accounts.

# 🌿 Git Commands Guide

This guide documents the Git commands used in the Laravel Boilerplate project, following the conventions specified in Qoder.md.

## 📋 Basic Git Workflow

### 1. Check Current Status
``bash
git status
```

### 2. Add Files to Staging
```bash
# Add specific files
git add app/Http/Controllers/Auth/*.php
git add resources/js/pages/auth/Register.vue
git add tests/Feature/Auth/RegistrationTest.php

# Add all changes
git add .
```

### 3. Commit Changes
```bash
# Commit with conventional commit message
git commit -m "feat: enhance authentication controllers to use BaseController and integrate with UserService" -m "Extended all authentication controllers to use our custom BaseController for consistent error handling and messaging. Integrated registration flow with UserService and CreateUserDTO for proper password hashing. Added phone number field to registration form with validation." -m "Updated frontend registration form to include phone number field. Enhanced error handling in all authentication controllers. Fixed registration test to include phone number field."
```

### 4. Push Changes
```bash
# Push to remote repository
git push origin git-workflow-improvements
```

## 🌿 Branch Management

### 1. Create a New Feature Branch
```bash
git checkout -b feature/staff-management
```

### 2. Switch Between Branches
```bash
# Switch to main branch
git checkout main

# Switch to feature branch
git checkout feature/staff-management
```

### 3. List All Branches
```bash
git branch
```

### 4. Delete a Branch
```bash
# Delete local branch
git branch -d feature/staff-management

# Delete remote branch
git push origin --delete feature/staff-management
```

## 🔄 Synchronization

### 1. Pull Latest Changes
```bash
# Pull from remote main branch
git pull origin main
```

### 2. Fetch All Remote Branches
```bash
git fetch --all
```

## 🔍 History and Inspection

### 1. View Commit History
```bash
# View commit history
git log

# View compact commit history
git log --oneline

# View commit history with file changes
git log --stat
```

### 2. View Differences
```bash
# View unstaged changes
git diff

# View staged changes
git diff --staged

# View differences between branches
git diff main feature/staff-management
```

## 🧹 Cleanup

### 1. Discard Changes
```bash
# Discard changes in working directory
git restore app/Http/Controllers/Auth/RegisteredUserController.php

# Unstage a file
git restore --staged app/Http/Controllers/Auth/RegisteredUserController.php
```

### 2. Reset Commits
```bash
# Reset to previous commit (keep changes)
git reset HEAD~1

# Reset to previous commit (discard changes)
git reset --hard HEAD~1
```

## 🎯 GitHub Repository Management

### 1. Remote Repository Setup
```bash
# Add remote repository (must be created manually on GitHub first)
git remote add origin https://github.com/yonasayfu/MyLaravelBoilerPlate.git

# Verify remote repository
git remote -v
```

### 2. Push to New Remote Repository
```bash
# Push all branches to remote repository
git push -u origin --all

# Push all tags to remote repository
git push -u origin --tags
```

## 📝 Conventional Commits Format

We follow the conventional commits format for all commit messages:

### Commit Types
- `feat:` - A new feature
- `fix:` - A bug fix
- `docs:` - Documentation only changes
- `style:` - Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc)
- `refactor:` - A code change that neither fixes a bug nor adds a feature
- `perf:` - A code change that improves performance
- `test:` - Adding missing tests or correcting existing tests
- `build:` - Changes that affect the build system or external dependencies
- `ci:` - Changes to our CI configuration files and scripts
- `chore:` - Other changes that don't modify src or test files
- `revert:` - Reverts a previous commit

### Example Commit Messages
```bash
git commit -m "feat: add staff model with user relationship"
git commit -m "fix: resolve validation issue in staff service"
git commit -m "test: add unit tests for staff controller"
git commit -m "docs: update architecture documentation"
```

## 📁 Multi-Account Git Configuration

For developers managing multiple GitHub accounts:

### 1. Configure SSH Keys
```bash
# Generate SSH key for each account
ssh-keygen -t rsa -b 4096 -C "yonasayfu28@gmail.com"
ssh-keygen -t rsa -b 4096 -C "elefenshyona@gmail.com"
ssh-keygen -t rsa -b 4096 -C "guangutsemera@gmail.com"
```

### 2. Configure SSH Config
Edit `~/.ssh/config`:
```
# yonasayfu account
Host github-yonasayfu
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_rsa_yonasayfu

# Elefensh-Yona account
Host github-elefensh
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_rsa_elefensh

# guangutsemera account
Host github-guangutsemera
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_rsa_guangutsemera
```

### 3. Clone Repositories with Specific Accounts
```bash
# Clone using yonasayfu account
git clone git@github-yonasayfu:yonasayfu/MyLaravelBoilerPlate.git

# Clone using Elefensh-Yona account
git clone git@github-elefensh:Elefensh-Yona/MyLaravelBoilerPlate.git
```

## ⚠️ Important Notes

1. **GitHub Repository Creation**: Remote repositories on GitHub must be manually created before adding them as git remotes. The `git remote add` command does not create repositories automatically.

2. **Branch Naming**: Use descriptive branch names with prefixes:
   - `feature/` - for new features
   - `bugfix/` - for bug fixes
   - `hotfix/` - for urgent fixes
   - `release/` - for releases

3. **Commit Frequency**: Make at least one commit for every 10 code changes to maintain a clear history.

4. **Documentation Updates**: Always update tracking documents (PHASE_TRACKING.md, ROADMAP_AND_PROGRESS.md) after implementing features.
