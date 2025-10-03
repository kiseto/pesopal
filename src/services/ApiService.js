// API Service for PesoPal
const API_BASE_URL = 'http://localhost/pesopal/src/api';

class ApiService {
    
    // Test database connection
    static async testConnection() {
        try {
            const response = await fetch(`${API_BASE_URL}/test-connection.php`);
            return await response.json();
        } catch (error) {
            console.error('Connection test failed:', error);
            return { success: false, message: 'Connection failed' };
        }
    }

    // User Registration
    static async register(userData) {
        try {
            const response = await fetch(`${API_BASE_URL}/auth/register.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Registration error:', error);
            return { success: false, message: 'Registration failed' };
        }
    }

    // User Login
    static async login(credentials) {
        try {
            const response = await fetch(`${API_BASE_URL}/auth/login.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(credentials)
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, message: 'Login failed' };
        }
    }

    // User Logout
    static async logout() {
        try {
            const response = await fetch(`${API_BASE_URL}/auth/logout.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Logout error:', error);
            return { success: false, message: 'Logout failed' };
        }
    }

    // Check Session
    static async checkSession() {
        try {
            const response = await fetch(`${API_BASE_URL}/auth/check-session.php`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Session check error:', error);
            return { success: false, message: 'Session check failed' };
        }
    }

    // Get Dashboard Summary
    static async getDashboardSummary() {
        try {
            console.log('Making request to:', `${API_BASE_URL}/dashboard/summary.php`)
            const response = await fetch(`${API_BASE_URL}/dashboard/summary.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            console.log('Response status:', response.status, 'OK:', response.ok)
            
            const rawText = await response.text();
            console.log('Raw response:', rawText)
            
            try {
                const data = JSON.parse(rawText);
                console.log('Parsed data:', data)
                return data;
            } catch (parseError) {
                console.error('JSON parse error:', parseError)
                console.error('Raw text that failed to parse:', rawText)
                return { success: false, message: 'Invalid JSON response' };
            }
        } catch (error) {
            console.error('Dashboard summary error:', error);
            return { success: false, message: 'Failed to load dashboard data' };
        }
    }

    // Get User Profile
    static async getUserProfile() {
        try {
            const response = await fetch(`${API_BASE_URL}/user/profile.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('User profile error:', error);
            return { success: false, message: 'Failed to load user profile' };
        }
    }

    // Get Transactions
    static async getTransactions() {
        try {
            const response = await fetch(`${API_BASE_URL}/transactions/list.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get transactions error:', error);
            return { success: false, message: 'Failed to load transactions' };
        }
    }

    // Add Transaction
    static async addTransaction(transactionData) {
        try {
            const response = await fetch(`${API_BASE_URL}/transactions/add.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(transactionData)
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Add transaction error:', error);
            return { success: false, message: 'Failed to add transaction' };
        }
    }

    // Update Transaction
    static async updateTransaction(transactionId, transactionData) {
        try {
            const response = await fetch(`${API_BASE_URL}/transactions/update.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: transactionId,
                    ...transactionData
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Update transaction error:', error);
            return { success: false, message: 'Failed to update transaction' };
        }
    }

    // Delete Transaction
    static async deleteTransaction(transactionId) {
        try {
            const response = await fetch(`${API_BASE_URL}/transactions/delete.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: transactionId
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Delete transaction error:', error);
            return { success: false, message: 'Failed to delete transaction' };
        }
    }

    // Debug Transactions
    static async debugTransactions() {
        try {
            const response = await fetch(`${API_BASE_URL}/debug/transactions.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Debug transactions error:', error);
            return { success: false, message: 'Debug failed' };
        }
    }

    // Create Sample Transactions
    static async createSampleTransactions() {
        try {
            const response = await fetch(`${API_BASE_URL}/debug/create-sample-transactions.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Create sample transactions error:', error);
            return { success: false, message: 'Failed to create sample transactions' };
        }
    }

    // Test Simple API
    static async testSimple() {
        try {
            const response = await fetch(`${API_BASE_URL}/test-simple.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const text = await response.text();
            console.log('Raw response:', text);
            
            const data = JSON.parse(text);
            return data;
        } catch (error) {
            console.error('Test simple error:', error);
            return { success: false, message: 'Test failed', error: error.message };
        }
    }

    // Test Transactions Debug
    static async testTransactionsDebug() {
        try {
            const response = await fetch(`${API_BASE_URL}/transactions/list-debug.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const text = await response.text();
            console.log('Transactions debug raw response:', text);
            
            const data = JSON.parse(text);
            return data;
        } catch (error) {
            console.error('Test transactions debug error:', error);
            return { success: false, message: 'Transactions debug failed', error: error.message };
        }
    }

    // Get Invoices
    static async getInvoices() {
        try {
            const response = await fetch(`${API_BASE_URL}/invoices/list.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get invoices error:', error);
            return { success: false, message: 'Failed to load invoices' };
        }
    }

    // Add Invoice
    static async addInvoice(invoiceData) {
        try {
            const response = await fetch(`${API_BASE_URL}/invoices/add.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(invoiceData)
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Add invoice error:', error);
            return { success: false, message: 'Failed to add invoice' };
        }
    }

    // Get Receipts
    static async getReceipts() {
        try {
            const response = await fetch(`${API_BASE_URL}/receipts/list.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get receipts error:', error);
            return { success: false, message: 'Failed to load receipts' };
        }
    }

    // Add Receipt
    static async addReceipt(receiptData) {
        try {
            const response = await fetch(`${API_BASE_URL}/receipts/add.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(receiptData)
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Add receipt error:', error);
            return { success: false, message: 'Failed to add receipt' };
        }
    }

    // Get Invoice Summary
    static async getInvoiceSummary() {
        try {
            const response = await fetch(`${API_BASE_URL}/invoices/summary.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get invoice summary error:', error);
            return { success: false, message: 'Failed to load invoice summary' };
        }
    }

    // Debug Invoices Data
    static async debugInvoicesData() {
        try {
            const response = await fetch(`${API_BASE_URL}/debug/invoices-data.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Debug invoices data error:', error);
            return { success: false, message: 'Debug failed' };
        }
    }

    // Create Sample Invoices and Receipts
    static async createSampleInvoices() {
        try {
            const response = await fetch(`${API_BASE_URL}/debug/create-sample-invoices.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Create sample invoices error:', error);
            return { success: false, message: 'Failed to create sample invoices' };
        }
    }

    // Get Raw Database Data for Debugging
    static async getRawInvoicesData() {
        try {
            const response = await fetch(`${API_BASE_URL}/debug/raw-invoices-data.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get raw invoices data error:', error);
            return { success: false, message: 'Failed to get raw data' };
        }
    }

    // Update Invoice
    static async updateInvoice(invoiceId, invoiceData) {
        try {
            const response = await fetch(`${API_BASE_URL}/invoices/update.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: invoiceId,
                    ...invoiceData
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Update invoice error:', error);
            return { success: false, message: 'Failed to update invoice' };
        }
    }

    // Update Receipt
    static async updateReceipt(receiptId, receiptData) {
        try {
            const response = await fetch(`${API_BASE_URL}/receipts/update.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: receiptId,
                    ...receiptData
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Update receipt error:', error);
            return { success: false, message: 'Failed to update receipt' };
        }
    }

    // Delete Invoice
    static async deleteInvoice(invoiceId) {
        try {
            const response = await fetch(`${API_BASE_URL}/invoices/delete.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: invoiceId
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Delete invoice error:', error);
            return { success: false, message: 'Failed to delete invoice' };
        }
    }

    // Delete Receipt
    static async deleteReceipt(receiptId) {
        try {
            const response = await fetch(`${API_BASE_URL}/receipts/delete.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: receiptId
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Delete receipt error:', error);
            return { success: false, message: 'Failed to delete receipt' };
        }
    }

    // Add Receipt with File Upload
    static async addReceiptWithFile(formData) {
        try {
            const response = await fetch(`${API_BASE_URL}/receipts/add-with-file.php`, {
                method: 'POST',
                credentials: 'include',
                // Don't set Content-Type header for FormData, browser will set it automatically
                body: formData
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Add receipt with file error:', error);
            return { success: false, message: 'Failed to add receipt with file' };
        }
    }

    // Get Budget Summary
    static async getBudgetSummary() {
        try {
            const response = await fetch(`${API_BASE_URL}/budget/summary.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get budget summary error:', error);
            return { success: false, message: 'Failed to load budget summary' };
        }
    }

    // Get Budget Categories
    static async getBudgetCategories() {
        try {
            const response = await fetch(`${API_BASE_URL}/budget/categories.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get budget categories error:', error);
            return { success: false, message: 'Failed to load budget categories' };
        }
    }

    // Add Budget Category
    static async addBudgetCategory(categoryData) {
        try {
            const response = await fetch(`${API_BASE_URL}/budget/add-category.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(categoryData)
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Add budget category error:', error);
            return { success: false, message: 'Failed to add budget category' };
        }
    }

    // Get Savings Goals
    static async getSavingsGoals() {
        try {
            const response = await fetch(`${API_BASE_URL}/budget/savings-goals.php`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get savings goals error:', error);
            return { success: false, message: 'Failed to load savings goals' };
        }
    }

    // Add Savings Goal
    static async addSavingsGoal(goalData) {
        try {
            const response = await fetch(`${API_BASE_URL}/budget/add-savings-goal.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(goalData)
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Add savings goal error:', error);
            return { success: false, message: 'Failed to add savings goal' };
        }
    }

    // Update Budget Category
    static async updateBudgetCategory(categoryId, categoryData) {
        try {
            const response = await fetch(`${API_BASE_URL}/budget/categories/update.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: categoryId,
                    ...categoryData
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Update budget category error:', error);
            return { success: false, message: 'Failed to update budget category' };
        }
    }

    // Update Savings Goal
    static async updateSavingsGoal(goalId, goalData) {
        try {
            const response = await fetch(`${API_BASE_URL}/budget/goals/update.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: goalId,
                    ...goalData
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Update savings goal error:', error);
            return { success: false, message: 'Failed to update savings goal' };
        }
    }

    // Update Budget Category Spent Amount
    static async updateBudgetCategorySpent(categoryId, amount) {
        try {
            const response = await fetch(`${API_BASE_URL}/budget/categories/update-spent.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: categoryId,
                    amount: amount
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Update budget category spent error:', error);
            return { success: false, message: 'Failed to update budget category spent amount' };
        }
    }

    // Update Savings Goal Saved Amount
    static async updateSavingsGoalSaved(goalId, amount) {
        try {
            const response = await fetch(`${API_BASE_URL}/budget/goals/update-saved.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: goalId,
                    amount: amount
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Update savings goal saved error:', error);
            return { success: false, message: 'Failed to update savings goal saved amount' };
        }
    }

    // Get Monthly Spending Data for Reports
    static async getMonthlySpending(range = 365) {
        try {
            console.log('Making request to:', `${API_BASE_URL}/reports/monthly-spending.php?range=${range}`)
            const response = await fetch(`${API_BASE_URL}/reports/monthly-spending.php?range=${range}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            console.log('Monthly spending response status:', response.status, 'OK:', response.ok)
            
            const rawText = await response.text();
            console.log('Raw monthly spending response:', rawText)
            
            try {
                const data = JSON.parse(rawText);
                console.log('Parsed monthly spending data:', data)
                return data;
            } catch (parseError) {
                console.error('JSON parse error:', parseError)
                console.error('Raw text that failed to parse:', rawText)
                return { success: false, message: 'Invalid JSON response' };
            }
        } catch (error) {
            console.error('Monthly spending error:', error);
            return { success: false, message: 'Failed to load monthly spending data' };
        }
    }

    // Get Category Spending Data for Reports
    static async getCategorySpending(range = 365) {
        try {
            console.log('Making request to:', `${API_BASE_URL}/reports/category-spending.php?range=${range}`)
            const response = await fetch(`${API_BASE_URL}/reports/category-spending.php?range=${range}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            console.log('Category spending response status:', response.status, 'OK:', response.ok)
            
            const rawText = await response.text();
            console.log('Raw category spending response:', rawText)
            
            try {
                const data = JSON.parse(rawText);
                console.log('Parsed category spending data:', data)
                return data;
            } catch (parseError) {
                console.error('JSON parse error:', parseError)
                console.error('Raw text that failed to parse:', rawText)
                return { success: false, message: 'Invalid JSON response' };
            }
        } catch (error) {
            console.error('Category spending error:', error);
            return { success: false, message: 'Failed to load category spending data' };
        }
    }

    // Get Comprehensive Report Data (transactions, invoices, budgets, savings)
    static async getComprehensiveReportData(range = 365) {
        try {
            console.log('Making request to:', `${API_BASE_URL}/reports/comprehensive-data.php?range=${range}`)
            const response = await fetch(`${API_BASE_URL}/reports/comprehensive-data.php?range=${range}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            console.log('Comprehensive report response status:', response.status, 'OK:', response.ok)
            
            const rawText = await response.text();
            console.log('Raw comprehensive report response:', rawText)
            
            try {
                const data = JSON.parse(rawText);
                console.log('Parsed comprehensive report data:', data)
                return data;
            } catch (parseError) {
                console.error('JSON parse error:', parseError)
                console.error('Raw text that failed to parse:', rawText)
                return { success: false, message: 'Invalid JSON response' };
            }
        } catch (error) {
            console.error('Comprehensive report error:', error);
            return { success: false, message: 'Failed to load comprehensive report data' };
        }
    }

}

export default ApiService;