## Authentification


I'll provide three widely-used authentication patterns with example implementations. These are OAuth 2.0, JWT (JSON Web Tokens), and session-based authentication.



```javascript
// 1. OAuth 2.0 Implementation (using Node.js with Passport)
const express = require('express');
const passport = require('passport');
const GoogleStrategy = require('passport-google-oauth20').Strategy;

const app = express();

passport.use(new GoogleStrategy({
    clientID: process.env.GOOGLE_CLIENT_ID,
    clientSecret: process.env.GOOGLE_CLIENT_SECRET,
    callbackURL: "http://localhost:3000/auth/google/callback"
  },
  function(accessToken, refreshToken, profile, cb) {
    User.findOrCreate({ googleId: profile.id }, function (err, user) {
      return cb(err, user);
    });
  }
));

app.get('/auth/google',
  passport.authenticate('google', { scope: ['profile', 'email'] })
);

app.get('/auth/google/callback', 
  passport.authenticate('google', { failureRedirect: '/login' }),
  function(req, res) {
    res.redirect('/');
  }
);

// 2. JWT Authentication Implementation
const jwt = require('jsonwebtoken');
const bcrypt = require('bcrypt');

class AuthService {
  constructor() {
    this.secretKey = process.env.JWT_SECRET_KEY;
  }

  async login(username, password) {
    // Find user in database
    const user = await User.findOne({ username });
    if (!user) {
      throw new Error('User not found');
    }

    // Verify password
    const isValid = await bcrypt.compare(password, user.password);
    if (!isValid) {
      throw new Error('Invalid password');
    }

    // Generate JWT token
    const token = jwt.sign(
      { userId: user.id, username: user.username },
      this.secretKey,
      { expiresIn: '24h' }
    );

    return { token, user };
  }

  verifyToken(token) {
    try {
      const decoded = jwt.verify(token, this.secretKey);
      return decoded;
    } catch (err) {
      throw new Error('Invalid token');
    }
  }
}

// Middleware to protect routes
const authMiddleware = (req, res, next) => {
  const token = req.headers.authorization?.split(' ')[1];
  if (!token) {
    return res.status(401).json({ message: 'No token provided' });
  }

  try {
    const decoded = authService.verifyToken(token);
    req.user = decoded;
    next();
  } catch (err) {
    return res.status(401).json({ message: 'Invalid token' });
  }
};

// 3. Session-based Authentication
const express = require('express');
const session = require('express-session');
const bcrypt = require('bcrypt');

const app = express();

app.use(session({
  secret: process.env.SESSION_SECRET,
  resave: false,
  saveUninitialized: false,
  cookie: { 
    secure: process.env.NODE_ENV === 'production',
    maxAge: 24 * 60 * 60 * 1000 // 24 hours
  }
}));

class SessionAuth {
  async login(req, username, password) {
    // Find user in database
    const user = await User.findOne({ username });
    if (!user) {
      throw new Error('User not found');
    }

    // Verify password
    const isValid = await bcrypt.compare(password, user.password);
    if (!isValid) {
      throw new Error('Invalid password');
    }

    // Set session data
    req.session.userId = user.id;
    req.session.username = user.username;
    
    return user;
  }

  logout(req) {
    return new Promise((resolve) => {
      req.session.destroy((err) => {
        if (err) {
          console.error('Session destruction error:', err);
        }
        resolve();
      });
    });
  }
}

// Session middleware
const sessionMiddleware = (req, res, next) => {
  if (!req.session.userId) {
    return res.status(401).json({ message: 'Not authenticated' });
  }
  next();
};

```

I've provided implementations for three popular authentication methods:

1. OAuth 2.0
- Uses Passport.js for Google authentication
- Handles authentication flow with external provider
- Includes callback handling and user creation

2. JWT (JSON Web Tokens)
- Includes token generation and verification
- Password hashing with bcrypt
- Middleware for protecting routes
- Token-based session management

3. Session-based Authentication
- Uses express-session for session management
- Includes secure cookie configuration
- Complete login/logout functionality
- Session middleware for route protection

Each solution includes security best practices like:
- Environment variable usage for secrets
- Proper password hashing
- Secure session configuration
- Error handling
- Middleware protection

Would you like me to explain any specific part in more detail or discuss the pros and cons of each approach?

// 1. OAuth 2.0 Implementation (using Node.js with Passport)
const express = require('express');
const passport = require('passport');
const GoogleStrategy = require('passport-google-oauth20').Strategy;

const app = express();

passport.use(new GoogleStrategy({
    clientID: process.env.GOOGLE_CLIENT_ID,
    clientSecret: process.env.GOOGLE_CLIENT_SECRET,
    callbackURL: "http://localhost:3000/auth/google/callback"
  },
  function(accessToken, refreshToken, profile, cb) {
    User.findOrCreate({ googleId: profile.id }, function (err, user) {
      return cb(err, user);
    });
  }
));

app.get('/auth/google',
  passport.authenticate('google', { scope: ['profile', 'email'] })
);

app.get('/auth/google/callback', 
  passport.authenticate('google', { failureRedirect: '/login' }),
  function(req, res) {
    res.redirect('/');
  }
);

// 2. JWT Authentication Implementation
const jwt = require('jsonwebtoken');
const bcrypt = require('bcrypt');

class AuthService {
  constructor() {
    this.secretKey = process.env.JWT_SECRET_KEY;
  }

  async login(username, password) {
    // Find user in database
    const user = await User.findOne({ username });
    if (!user) {
      throw new Error('User not found');
    }

    // Verify password
    const isValid = await bcrypt.compare(password, user.password);
    if (!isValid) {
      throw new Error('Invalid password');
    }

    // Generate JWT token
    const token = jwt.sign(
      { userId: user.id, username: user.username },
      this.secretKey,
      { expiresIn: '24h' }
    );

    return { token, user };
  }

  verifyToken(token) {
    try {
      const decoded = jwt.verify(token, this.secretKey);
      return decoded;
    } catch (err) {
      throw new Error('Invalid token');
    }
  }
}

// Middleware to protect routes
const authMiddleware = (req, res, next) => {
  const token = req.headers.authorization?.split(' ')[1];
  if (!token) {
    return res.status(401).json({ message: 'No token provided' });
  }

  try {
    const decoded = authService.verifyToken(token);
    req.user = decoded;
    next();
  } catch (err) {
    return res.status(401).json({ message: 'Invalid token' });
  }
};

// 3. Session-based Authentication
const express = require('express');
const session = require('express-session');
const bcrypt = require('bcrypt');

const app = express();

app.use(session({
  secret: process.env.SESSION_SECRET,
  resave: false,
  saveUninitialized: false,
  cookie: { 
    secure: process.env.NODE_ENV === 'production',
    maxAge: 24 * 60 * 60 * 1000 // 24 hours
  }
}));

class SessionAuth {
  async login(req, username, password) {
    // Find user in database
    const user = await User.findOne({ username });
    if (!user) {
      throw new Error('User not found');
    }

    // Verify password
    const isValid = await bcrypt.compare(password, user.password);
    if (!isValid) {
      throw new Error('Invalid password');
    }

    // Set session data
    req.session.userId = user.id;
    req.session.username = user.username;
    
    return user;
  }

  logout(req) {
    return new Promise((resolve) => {
      req.session.destroy((err) => {
        if (err) {
          console.error('Session destruction error:', err);
        }
        resolve();
      });
    });
  }
}

// Session middleware
const sessionMiddleware = (req, res, next) => {
  if (!req.session.userId) {
    return res.status(401).json({ message: 'Not authenticated' });
  }
  next();
};