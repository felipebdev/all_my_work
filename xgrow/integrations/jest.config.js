module.exports = {
  roots: ['<rootDir>/src'],
  setupFiles: ['<rootDir>/.jest/setEnvVars.js'],
  setupFilesAfterEnv: ['<rootDir>/.jest/setJestDefaultMocks.js'],
  moduleNameMapper: {
    '@app/(.*)': '<rootDir>/src/$1'
  },
  moduleFileExtensions: ['js', 'mjs', 'cjs', 'jsx', 'ts', 'tsx', 'json', 'node'],
  preset: 'ts-jest',
  transform: {
    '^.+\\.(t|j)s$': 'ts-jest'
  },
  testEnvironment: 'node',
  coverageReporters: ['json-summary', 'text', 'lcov'],
  testResultsProcessor: 'jest-sonar-reporter',
  coverageDirectory: 'coverage',
  coverageThreshold: {
    global: 80
  },
  collectCoverageFrom: [
    '<rootDir>/src/**/*.{js,jsx,ts,tsx}',
    '!<rootDir>/src/**/collections/*.{js,jsx,ts,tsx}',
    '!<rootDir>/src/decorators/**.{js,jsx,ts,tsx}',
    '!<rootDir>/src/**/index.{js,jsx,ts,tsx}',
    '!<rootDir>/src/**/*.interface.{js,jsx,ts,tsx}',
    '!<rootDir>/src/**/*.*spec.{js,jsx,ts,tsx}',
    '!<rootDir>/src/**/*.mock.{js,jsx,ts,tsx}',
    '!<rootDir>/src/services/base.{js,jsx,ts,tsx}',
    '!<rootDir>/src/services/expo.{js,jsx,ts,tsx}',
    '!<rootDir>/src/config/*.{js,jsx,ts,tsx}',
    '!<rootDir>/src/testing-function.{js,jsx,ts,tsx}'
  ]
}
