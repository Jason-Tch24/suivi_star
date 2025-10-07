/**
 * Inline Editing System for User Management
 * Allows direct editing within the user management table
 */

class InlineEditor {
    constructor() {
        this.editingCell = null;
        this.originalValue = null;
        this.init();
    }
    
    init() {
        this.attachEventListeners();
        this.setupKeyboardShortcuts();
    }
    
    attachEventListeners() {
        // Double-click to edit
        document.addEventListener('dblclick', (e) => {
            const editableCell = e.target.closest('[data-editable]');
            if (editableCell && !this.editingCell) {
                this.startEdit(editableCell);
            }
        });
        
        // Click outside to save
        document.addEventListener('click', (e) => {
            if (this.editingCell && !this.editingCell.contains(e.target)) {
                this.saveEdit();
            }
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (this.editingCell) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.saveEdit();
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    this.cancelEdit();
                }
            }
        });
    }
    
    setupKeyboardShortcuts() {
        // F2 to edit focused cell
        document.addEventListener('keydown', (e) => {
            if (e.key === 'F2' && !this.editingCell) {
                const focusedCell = document.activeElement.closest('[data-editable]');
                if (focusedCell) {
                    e.preventDefault();
                    this.startEdit(focusedCell);
                }
            }
        });
    }
    
    startEdit(cell) {
        if (this.editingCell) {
            this.saveEdit();
        }
        
        this.editingCell = cell;
        this.originalValue = cell.textContent.trim();
        
        const fieldType = cell.dataset.editable;
        const fieldName = cell.dataset.field;
        const userId = cell.closest('tr').dataset.userId;
        
        // Create appropriate input element
        let input;
        
        switch (fieldType) {
            case 'text':
                input = this.createTextInput(this.originalValue);
                break;
            case 'email':
                input = this.createEmailInput(this.originalValue);
                break;
            case 'phone':
                input = this.createPhoneInput(this.originalValue);
                break;
            case 'select':
                input = this.createSelectInput(fieldName, this.originalValue);
                break;
            case 'role':
                input = this.createRoleSelect(this.originalValue);
                break;
            case 'status':
                input = this.createStatusSelect(this.originalValue);
                break;
            default:
                input = this.createTextInput(this.originalValue);
        }
        
        // Style the input
        this.styleInput(input, cell);
        
        // Replace cell content with input
        cell.innerHTML = '';
        cell.appendChild(input);
        
        // Focus and select
        input.focus();
        if (input.select) {
            input.select();
        }
        
        // Add editing class
        cell.classList.add('editing');
    }
    
    createTextInput(value) {
        const input = document.createElement('input');
        input.type = 'text';
        input.value = value;
        input.className = 'inline-edit-input';
        return input;
    }
    
    createEmailInput(value) {
        const input = document.createElement('input');
        input.type = 'email';
        input.value = value;
        input.className = 'inline-edit-input';
        return input;
    }
    
    createPhoneInput(value) {
        const input = document.createElement('input');
        input.type = 'tel';
        input.value = value;
        input.className = 'inline-edit-input';
        return input;
    }
    
    createSelectInput(fieldName, value) {
        const select = document.createElement('select');
        select.className = 'inline-edit-select';
        
        // Add options based on field name
        const options = this.getSelectOptions(fieldName);
        
        options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.textContent = option.label;
            if (option.value === value) {
                optionElement.selected = true;
            }
            select.appendChild(optionElement);
        });
        
        return select;
    }
    
    createRoleSelect(value) {
        const select = document.createElement('select');
        select.className = 'inline-edit-select';
        
        const roles = [
            { value: 'administrator', label: 'Administrator' },
            { value: 'pastor', label: 'Pastor' },
            { value: 'mds', label: 'MDS' },
            { value: 'mentor', label: 'Mentor' },
            { value: 'aspirant', label: 'Aspirant' }
        ];
        
        roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.value;
            option.textContent = role.label;
            if (role.value === value.toLowerCase()) {
                option.selected = true;
            }
            select.appendChild(option);
        });
        
        return select;
    }
    
    createStatusSelect(value) {
        const select = document.createElement('select');
        select.className = 'inline-edit-select';
        
        const statuses = [
            { value: 'active', label: 'Active' },
            { value: 'inactive', label: 'Inactive' },
            { value: 'suspended', label: 'Suspended' }
        ];
        
        statuses.forEach(status => {
            const option = document.createElement('option');
            option.value = status.value;
            option.textContent = status.label;
            if (status.value === value.toLowerCase()) {
                option.selected = true;
            }
            select.appendChild(option);
        });
        
        return select;
    }
    
    getSelectOptions(fieldName) {
        // Return options based on field name
        const optionMap = {
            department: [
                { value: 'Administration', label: 'Administration' },
                { value: 'Training', label: 'Training' },
                { value: 'Coordination', label: 'Coordination' },
                { value: 'Assessment', label: 'Assessment' }
            ],
            certification_level: [
                { value: 'Basic', label: 'Basic' },
                { value: 'Intermediate', label: 'Intermediate' },
                { value: 'Advanced', label: 'Advanced' },
                { value: 'Expert', label: 'Expert' }
            ],
            experience_level: [
                { value: 'Beginner', label: 'Beginner' },
                { value: 'Intermediate', label: 'Intermediate' },
                { value: 'Advanced', label: 'Advanced' },
                { value: 'Expert', label: 'Expert' }
            ]
        };
        
        return optionMap[fieldName] || [];
    }
    
    styleInput(input, cell) {
        const cellRect = cell.getBoundingClientRect();
        
        input.style.width = '100%';
        input.style.height = '100%';
        input.style.border = '2px solid var(--primary-500)';
        input.style.borderRadius = 'var(--radius-md)';
        input.style.padding = 'var(--space-2)';
        input.style.fontSize = 'var(--text-sm)';
        input.style.fontFamily = 'inherit';
        input.style.backgroundColor = 'white';
        input.style.outline = 'none';
        input.style.boxShadow = '0 0 0 3px var(--primary-100)';
    }
    
    async saveEdit() {
        if (!this.editingCell) return;
        
        const input = this.editingCell.querySelector('input, select');
        const newValue = input.value.trim();
        const fieldName = this.editingCell.dataset.field;
        const userId = this.editingCell.closest('tr').dataset.userId;
        
        // Validate the new value
        if (!this.validateValue(fieldName, newValue)) {
            this.showError('Invalid value entered');
            return;
        }
        
        // Check if value actually changed
        if (newValue === this.originalValue) {
            this.cancelEdit();
            return;
        }
        
        // Show loading state
        this.showLoading();
        
        try {
            // Send update request
            const response = await this.updateUser(userId, fieldName, newValue);
            
            if (response.success) {
                // Update the cell display
                this.updateCellDisplay(newValue);
                this.finishEdit();
                this.showSuccess('Updated successfully');
            } else {
                throw new Error(response.message || 'Update failed');
            }
        } catch (error) {
            this.showError(error.message);
            this.cancelEdit();
        }
    }
    
    cancelEdit() {
        if (!this.editingCell) return;
        
        // Restore original value
        this.updateCellDisplay(this.originalValue);
        this.finishEdit();
    }
    
    finishEdit() {
        if (this.editingCell) {
            this.editingCell.classList.remove('editing');
            this.editingCell = null;
            this.originalValue = null;
        }
    }
    
    updateCellDisplay(value) {
        const fieldType = this.editingCell.dataset.editable;
        
        if (fieldType === 'role') {
            // Update role badge
            const roleClass = `badge-${value.toLowerCase()}`;
            this.editingCell.innerHTML = `<span class="badge ${roleClass}">${this.capitalizeFirst(value)}</span>`;
        } else if (fieldType === 'status') {
            // Update status badge
            const statusClass = value === 'active' ? 'badge-success' : 'badge-gray';
            this.editingCell.innerHTML = `<span class="badge ${statusClass}">${this.capitalizeFirst(value)}</span>`;
        } else {
            // Regular text update
            this.editingCell.textContent = value;
        }
    }
    
    validateValue(fieldName, value) {
        switch (fieldName) {
            case 'email':
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            case 'phone':
                return value === '' || /^[\d\s\-\+\(\)]+$/.test(value);
            case 'first_name':
            case 'last_name':
                return value.length > 0 && value.length <= 50;
            default:
                return true;
        }
    }
    
    async updateUser(userId, fieldName, value) {
        const formData = new FormData();
        formData.append('action', 'inline_update');
        formData.append('user_id', userId);
        formData.append('field', fieldName);
        formData.append('value', value);
        
        const response = await fetch('users.php', {
            method: 'POST',
            body: formData
        });
        
        return await response.json();
    }
    
    showLoading() {
        if (this.editingCell) {
            this.editingCell.innerHTML = '<div class="inline-loading">Saving...</div>';
        }
    }
    
    showSuccess(message) {
        this.showNotification(message, 'success');
    }
    
    showError(message) {
        this.showNotification(message, 'error');
    }
    
    showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `inline-notification ${type}`;
        notification.textContent = message;
        
        // Style the notification
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.padding = 'var(--space-3) var(--space-4)';
        notification.style.borderRadius = 'var(--radius-lg)';
        notification.style.fontSize = 'var(--text-sm)';
        notification.style.fontWeight = '500';
        notification.style.zIndex = '1000';
        notification.style.boxShadow = 'var(--shadow-lg)';
        
        if (type === 'success') {
            notification.style.backgroundColor = '#d1fae5';
            notification.style.color = '#065f46';
            notification.style.border = '1px solid #a7f3d0';
        } else {
            notification.style.backgroundColor = '#fee2e2';
            notification.style.color = '#991b1b';
            notification.style.border = '1px solid #fecaca';
        }
        
        // Add to page
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }
    
    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
}

// Initialize inline editor when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new InlineEditor();
});

// Add CSS styles for inline editing
const inlineEditingStyles = `
    <style>
        [data-editable] {
            cursor: pointer;
            position: relative;
            transition: background-color var(--transition-fast);
        }
        
        [data-editable]:hover {
            background-color: var(--gray-50);
        }
        
        [data-editable].editing {
            background-color: var(--primary-50);
        }
        
        .inline-edit-input,
        .inline-edit-select {
            width: 100%;
            border: 2px solid var(--primary-500);
            border-radius: var(--radius-md);
            padding: var(--space-2);
            font-size: var(--text-sm);
            font-family: inherit;
            background-color: white;
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-100);
        }
        
        .inline-loading {
            color: var(--primary-600);
            font-style: italic;
            font-size: var(--text-sm);
        }
        
        .inline-notification {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Tooltip for editable cells */
        [data-editable]::after {
            content: 'Double-click to edit';
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gray-800);
            color: white;
            padding: var(--space-1) var(--space-2);
            border-radius: var(--radius-sm);
            font-size: var(--text-xs);
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity var(--transition-fast);
            z-index: 1000;
        }
        
        [data-editable]:hover::after {
            opacity: 1;
        }
        
        [data-editable].editing::after {
            display: none;
        }
    </style>
`;

// Inject styles
document.head.insertAdjacentHTML('beforeend', inlineEditingStyles);
