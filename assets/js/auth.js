// assets/js/auth.js

/**
 * Handles user authentication (registration, login, logout).
 */

const auth = {
  /**
   * Registers a new user.
   * @param {string} username - The username of the new user.
   * @param {string} email - The email of the new user.
   * @param {string} password - The password of the new user.
   * @returns {Promise<object>} - A promise that resolves with the registration response or rejects with an error.
   */
  register: async (username, email, password) => {
    try {
      const response = await fetch('api/register.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, email, password })
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Registration failed');
      }

      return data;
    } catch (error) {
      console.error('Registration error:', error);
      throw error;
    }
  },

  /**
   * Logs in an existing user.
   * @param {string} username - The username of the user.
   * @param {string} password - The password of the user.
   * @returns {Promise<object>} - A promise that resolves with the login response or rejects with an error.
   */
  login: async (username, password) => {
    try {
      const response = await fetch('api/login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, password })
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Login failed');
      }

      // Store the token or user data in local storage if needed
      localStorage.setItem('user', JSON.stringify(data.user));

      return data;
    } catch (error) {
      console.error('Login error:', error);
      throw error;
    }
  },

  /**
   * Logs out the current user.
   * @returns {Promise<object>} - A promise that resolves with the logout response or rejects with an error.
   */
  logout: async () => {
    try {
      const response = await fetch('api/logout.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        }
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Logout failed');
      }

      // Remove user data from local storage
      localStorage.removeItem('user');

      return data;
    } catch (error) {
      console.error('Logout error:', error);
      throw error;
    }
  },

  /**
   * Checks if the user is currently logged in.
   * @returns {boolean} - True if the user is logged in, false otherwise.
   */
  isLoggedIn: () => {
    return localStorage.getItem('user') !== null;
  },

  /**
   * Gets the current user from local storage.
   * @returns {object|null} - The user object if logged in, null otherwise.
   */
  getCurrentUser: () => {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }
};

export default auth;