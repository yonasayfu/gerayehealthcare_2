#!/bin/bash

# Git Sync Script for Geraye Healthcare Project
# This script helps manage both web and mobile repositories

echo "========================================="
echo "Geraye Healthcare Git Management Script"
echo "========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[STATUS]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if directory exists
check_directory() {
    if [ ! -d "$1" ]; then
        print_error "Directory $1 does not exist"
        return 1
    fi
    return 0
}

# Function to get current branch
get_current_branch() {
    git branch --show-current 2>/dev/null || echo "unknown"
}

# Function to get number of modified files
get_modified_count() {
    git status --porcelain 2>/dev/null | wc -l | tr -d ' '
}

# Function to sync a repository
sync_repository() {
    local repo_name=$1
    local repo_path=$2
    
    print_status "Processing $repo_name repository..."
    
    # Check if directory exists
    if ! check_directory "$repo_path"; then
        return 1
    fi
    
    # Navigate to repository
    cd "$repo_path" || return 1
    
    # Show current status
    local current_branch=$(get_current_branch)
    local modified_count=$(get_modified_count)
    
    print_status "$repo_name - Current branch: $current_branch"
    print_status "$repo_name - Modified files: $modified_count"
    
    # Show git status summary
    if [ "$modified_count" -gt 0 ]; then
        print_warning "$repo_name - Repository has uncommitted changes"
        git status --short
        echo ""
    else
        print_success "$repo_name - Repository is clean"
    fi
    
    # Show recent commits
    print_status "$repo_name - Recent commits:"
    git log --oneline -3 2>/dev/null || echo "No commits found"
    echo ""
}

# Function to stage and commit changes
commit_changes() {
    local repo_name=$1
    local repo_path=$2
    local commit_message=$3
    
    print_status "Committing changes in $repo_name..."
    
    # Check if directory exists
    if ! check_directory "$repo_path"; then
        return 1
    fi
    
    # Navigate to repository
    cd "$repo_path" || return 1
    
    # Check if there are changes
    local modified_count=$(get_modified_count)
    if [ "$modified_count" -eq 0 ]; then
        print_success "$repo_name - No changes to commit"
        return 0
    fi
    
    # Stage all changes
    print_status "$repo_name - Staging all changes..."
    git add .
    
    # Commit changes
    print_status "$repo_name - Committing changes..."
    git commit -m "$commit_message"
    
    print_success "$repo_name - Changes committed successfully"
}

# Function to push changes
push_changes() {
    local repo_name=$1
    local repo_path=$2
    
    print_status "Pushing changes for $repo_name..."
    
    # Check if directory exists
    if ! check_directory "$repo_path"; then
        return 1
    fi
    
    # Navigate to repository
    cd "$repo_path" || return 1
    
    # Get current branch
    local current_branch=$(get_current_branch)
    
    # Push changes
    print_status "$repo_name - Pushing to origin/$current_branch..."
    if git push origin "$current_branch"; then
        print_success "$repo_name - Changes pushed successfully"
    else
        print_error "$repo_name - Failed to push changes"
        return 1
    fi
}

# Function to pull latest changes
pull_changes() {
    local repo_name=$1
    local repo_path=$2
    
    print_status "Pulling latest changes for $repo_name..."
    
    # Check if directory exists
    if ! check_directory "$repo_path"; then
        return 1
    fi
    
    # Navigate to repository
    cd "$repo_path" || return 1
    
    # Get current branch
    local current_branch=$(get_current_branch)
    
    # Pull changes
    print_status "$repo_name - Pulling from origin/$current_branch..."
    if git pull origin "$current_branch"; then
        print_success "$repo_name - Latest changes pulled successfully"
    else
        print_error "$repo_name - Failed to pull changes"
        return 1
    fi
}

# Function to create backup tag
create_backup() {
    local repo_name=$1
    local repo_path=$2
    
    print_status "Creating backup for $repo_name..."
    
    # Check if directory exists
    if ! check_directory "$repo_path"; then
        return 1
    fi
    
    # Navigate to repository
    cd "$repo_path" || return 1
    
    # Create timestamp
    local timestamp=$(date +%Y%m%d-%H%M%S)
    local tag_name="backup-$timestamp"
    
    # Create tag
    if git tag "$tag_name"; then
        print_success "$repo_name - Backup tag created: $tag_name"
        
        # Push tag
        if git push origin "$tag_name"; then
            print_success "$repo_name - Backup tag pushed to remote"
        else
            print_warning "$repo_name - Failed to push backup tag to remote"
        fi
    else
        print_error "$repo_name - Failed to create backup tag"
        return 1
    fi
}

# Main script execution
main() {
    local web_path="/Users/yonassayfu/VSProject/gerayehealthcare"
    local mobile_path="/Users/yonassayfu/VSProject/gerayehealthcare/gerayehealthcare-mobile-app"
    
    case "$1" in
        status)
            echo "=== Repository Status ==="
            sync_repository "Web App" "$web_path"
            sync_repository "Mobile App" "$mobile_path"
            ;;
        commit)
            if [ -z "$2" ]; then
                print_error "Please provide a commit message"
                echo "Usage: $0 commit \"Your commit message\""
                exit 1
            fi
            
            echo "=== Committing Changes ==="
            commit_changes "Web App" "$web_path" "$2"
            commit_changes "Mobile App" "$mobile_path" "$2"
            ;;
        push)
            echo "=== Pushing Changes ==="
            push_changes "Web App" "$web_path"
            push_changes "Mobile App" "$mobile_path"
            ;;
        pull)
            echo "=== Pulling Changes ==="
            pull_changes "Web App" "$web_path"
            pull_changes "Mobile App" "$mobile_path"
            ;;
        backup)
            echo "=== Creating Backups ==="
            create_backup "Web App" "$web_path"
            create_backup "Mobile App" "$mobile_path"
            ;;
        sync)
            echo "=== Full Sync ==="
            pull_changes "Web App" "$web_path"
            pull_changes "Mobile App" "$mobile_path"
            commit_changes "Web App" "$web_path" "Auto-sync commit $(date)"
            commit_changes "Mobile App" "$mobile_path" "Auto-sync commit $(date)"
            push_changes "Web App" "$web_path"
            push_changes "Mobile App" "$mobile_path"
            ;;
        *)
            echo "Geraye Healthcare Git Management Script"
            echo ""
            echo "Usage: $0 [command]"
            echo ""
            echo "Commands:"
            echo "  status   - Show status of both repositories"
            echo "  commit   - Commit changes in both repositories"
            echo "  push     - Push changes to remote repositories"
            echo "  pull     - Pull latest changes from remote repositories"
            echo "  backup   - Create backup tags for both repositories"
            echo "  sync     - Full sync (pull -> commit -> push)"
            echo ""
            echo "Examples:"
            echo "  $0 status"
            echo "  $0 commit \"Add new mobile API endpoints\""
            echo "  $0 push"
            echo ""
            ;;
    esac
}

# Run main function with all arguments
main "$@"