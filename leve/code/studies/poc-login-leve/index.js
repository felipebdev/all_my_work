const {
  CognitoIdentityProviderClient,
  AdminCreateUserCommand,
  AdminGetUserCommand, 
  SignUpCommand,
  ConfirmSignUpCommand,
  InitiateAuthCommand,
  GetUserCommand,
  ForgotPasswordCommand,
  ConfirmForgotPasswordCommand,
  ChangePasswordCommand,
  GlobalSignOutCommand,
  AdminDeleteUserCommand,
  UpdateUserAttributesCommand,
  AdminInitiateAuthCommand,
  AdminRespondToAuthChallengeCommand,
  GetUserPoolMfaConfigCommand,
  SetUserPoolMfaConfigCommand,
  AdminSetUserPasswordCommand,  
  AdminResetUserPasswordCommand,
  ResendConfirmationCodeCommand,
} = require("@aws-sdk/client-cognito-identity-provider")

const cognitoClient = new CognitoIdentityProviderClient({
  // endpoint: 'http://localhost:4566',
  region:'us-east-2',
  credentials: {
    accessKeyId: 'AKIARMKE5KLTEU4GZFHD',
    secretAccessKey: '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'
  }
})

  // Pool Managment IDs
const UserPoolId = 'us-east-2_2JvuAV7JJ'
const ClientId = '1ulaj37cgatjqp9eqgglqol1ih'

// Mocked username props
const Username = 'felipebonazzi'
const Password = 'whP:XBk9'
const NewPassword = 'Fel@@@!32'
const Session = 'AYABeB-s1GYIaNdhqjZFxU2DnJgAHQABAAdTZXJ2aWNlABBDb2duaXRvVXNlclBvb2xzAAEAB2F3cy1rbXMAS2Fybjphd3M6a21zOnVzLWVhc3QtMjo0MTc1Njc5MDM0Njk6a2V5LzVjZDI0ZDRjLWVjNWItNGU4Ny05MGI2LTVkODdkOTZmY2RkMgC4AQIBAHjif3k0w30uAyP92ifoZ0jN6g50UW_KR0w9Vv2c_wlQAgFwMtmPl17Z4hh72iDEAyEbAAAAfjB8BgkqhkiG9w0BBwagbzBtAgEAMGgGCSqGSIb3DQEHATAeBglghkgBZQMEAS4wEQQMiUilmz0AValGoDLSAgEQgDuJM1ziUD_B3OMB661k-sMeSJTFs-W5L53FMQT2svrKd8AJgweV1jKA5eIBCJxap07il8aCMVwSiKfsZQIAAAAADAAAEAAAAAAAAAAAAAAAAADHqUw4Puu7uqWAYDCo2fBN_____wAAAAEAAAAAAAAAAAAAAAEAAAC9eUrXOyqgMdLk5Kdxk9wVYeBG3bShHkUctz_A2q0FPH-jc9734KkH-MWHtAwTOXADOOH48W-QGfDMQZc6f-sTkHAUoKduoIW6_s7aFCLj6BZT38FtkB-PTE-sWPCCfZXenshmILn1m2Jv33TYJ7wM84tUzd52ESA4XQLPMp7DNxZaPb5JuYjFvw49PmS8-cOitvDatsA2iGtFd0aXRYoPcFmZkx7hqDoKZoqtI_6xgJTxPgiU6-o6fiagx1G9sXN3fQZY3HY9DejGOUVc2w'
const SMS_MFA_CODE = '214968' //188562

// Mocked AWS Tokens
const ConfirmationCode = '246528'
const AccessToken = require('./access_token')

//não desparar e-mail/sms
const registerAdmin = async (event) => {
  const newUserCommand = new AdminCreateUserCommand({
    UserPoolId,
    Username,
    // TemporaryPassword: 'Fel@@!23',
    UserAttributes:  [
      {
        Name: 'email', 
        Value: 'doug_ss@live.com'
      }, 
      {
        Name: 'phone_number', 
        Value: '+5544999240667'
      },
      {
        Name: 'picture', 
        Value: 'pictureurl.img'
      },
    ],
    // MessageAction: 'RESEND'
  })

  try {
    const user = await cognitoClient.send(newUserCommand)
    return {
      status: 200,
      user
    }
  } catch (error) {
    console.log('registerAdmin error: ', error)
    throw error
  }
}

// const resendCode = async (event) => {
//   const resendCodeCommand = new ResendConfirmationCodeCommand({
//     Username,
//     UserPoolId,
//     ClientId
//   })

//   try {
//     const user = await cognitoClient.send(resendCodeCommand)
//     return {
//       status: 200,
//       user
//     }
//   } catch (error) {
//     console.log('resendCode error: ', error)
//     throw error
//   }
// }

// const resetPassword = async (event) => {

//   const findUserCommand = new AdminResetUserPasswordCommand({
//     UserPoolId,
//     Username,
//   })

//   try {
//     const user = await cognitoClient.send(findUserCommand)
//     return {
//       status: 200,
//       user
//     }
//   } catch (error) {
//     console.log('findUserAdmin error: ', error)
//     throw error
//   }

// }

const findUser = async (event) => {

  const findUserCommand = new AdminGetUserCommand({
    UserPoolId,
    Username
  })

  try {
    const user = await cognitoClient.send(findUserCommand)
    return {
      status: 200,
      user
    }
  } catch (error) {
    console.log('findUserAdmin error: ', error)
    throw error
  }

}

// const register = async (event) => {
//   const newUserCommand = new SignUpCommand({
//     ClientId,
//     Username,
//     Password,
//     UserAttributes:  [
//       {
//         Name: 'email', 
//         Value: 'doug_ss@live.com'
//       }, 
//       {
//         Name: 'phone_number', 
//         Value: '+5544999240667'
//       },
//       {
//         Name: 'picture', 
//         Value: 'pictureurl.img'
//       },
//     ]
//   })

//   try {
//     const user = await cognitoClient.send(newUserCommand)
//     return {
//       status: 200,
//       user
//     }
//   } catch (error) {
//     console.log('register error: ', error)
//     throw error
//   }

// }

// const confirmUser = async (event) => {
//   const confirmUserCommand = new ConfirmSignUpCommand({
//     ClientId,
//     Username,
//     ConfirmationCode
//   })

//   try {
//     const user = await cognitoClient.send(confirmUserCommand)
//     return {
//       status: 200,
//       user
//     }
//   } catch (error) {
//     console.log('confirmUser error: ', error)
//     throw error
//   }
// }

const loginWithMfa = async (event) => {

  const initateAuthCommand = new AdminInitiateAuthCommand({
    AuthFlow: 'ADMIN_USER_PASSWORD_AUTH',
    AuthParameters: {
      USERNAME: Username,
      PASSWORD: Password
    },
    ClientId,
    UserPoolId
  })

  try {
    const result = await cognitoClient.send(initateAuthCommand)
    return {
      status: 200,
      result
    }
  } catch (error) {
    console.log('loginWithMfa error: ', error)
    throw error
  }
}

const respondNewPasswordRequired = async (event) => {

  const respondNewPasswordCommand = new AdminRespondToAuthChallengeCommand({
    UserPoolId,
    ChallengeName: 'NEW_PASSWORD_REQUIRED',
    ChallengeResponses: { 
      NEW_PASSWORD: 'Fel@@@!21',
      USERNAME: Username
    },
    Session,
    ClientId,
   })
 
   try {
     const result = await cognitoClient.send(respondNewPasswordCommand)
     return {
       status: 200,
       result
     }
   } catch (error) {
     console.log('respondNewPasswordRequired error: ', error)
     throw error
   }
}


// const getUserMfa = async (event) => {

//   const getUserMfaCommand = new GetUserPoolMfaConfigCommand({
//    UserPoolId
//   })

//   try {
//     const result = await cognitoClient.send(getUserMfaCommand)
//     return {
//       status: 200,
//       result
//     }
//   } catch (error) {
//     console.log('getUserMfa error: ', error)
//     throw error
//   }
// }


const respondMfa = async (event) => {

  const initateAuthCommand = new AdminRespondToAuthChallengeCommand({
    ClientId,
    UserPoolId,
    Session,
    ChallengeName: 'SMS_MFA',
    ChallengeResponses: { 
      USERNAME: Username,
      SMS_MFA_CODE
    } 
  })

  try {
    const result = await cognitoClient.send(initateAuthCommand)
    return {
      status: 200,
      result
    }
  } catch (error) {
    console.log('loginWithMfa error: ', error)
    throw error
  }
}


// const login = async (event) => {
//   const initateAuthCommand = new AdminInitiateAuthCommand({
//     AuthFlow: 'USER_PASSWORD_AUTH',
//     AuthParameters: {
//       USERNAME: Username,
//       PASSWORD: Password
//     },
//     ClientId
//   })

//   try {
//     const session = await cognitoClient.send(initateAuthCommand)
//     return {
//       status: 200,
//       session
//     }
//   } catch (error) {
//     console.log('login error: ', error)
//     throw error
//   }
// }

const userDetails = async (event) => {
  const getUserCommand = new GetUserCommand({
    AccessToken
  })

  try {
    const user = await cognitoClient.send(getUserCommand)
    return {
      status: 200,
      user
    }
  } catch (error) {
    console.log('userDetails error: ', error)
    throw error
  }
} 

const changePassword = async (event) => {
  const changePasswordCommand = new ChangePasswordCommand({
    AccessToken,
    PreviousPassword: Password,
    ProposedPassword: NewPassword
  })

  try {
    const result = await cognitoClient.send(changePasswordCommand)
    return {
      status: 200,
      result
    }
  } catch (error) {
    console.log('changePassword error: ', error)
    throw error
  }
}

const forgotPasswordInit = async (event) => {
  const forgotPasswordCommand = new ForgotPasswordCommand({
    ClientId,
    Username,
  })

  try {
    const result = await cognitoClient.send(forgotPasswordCommand)
    return {
      status: 200,
      result
    }
  } catch (error) {
    console.log('forgotPasswordInit error: ', error)
    throw error
  }
}

const confirmForgotPassword = async (event) => {
  const confirmForgotPasswordCommand = new ConfirmForgotPasswordCommand({
    ClientId,
    ConfirmationCode,
    Password: NewPassword,
    Username
  })

  try {
    const result = await cognitoClient.send(confirmForgotPasswordCommand)
    return {
      status: 200,
      result
    }
  } catch (error) {
    console.log('confirmForgotPassword error: ', error)
    throw error
  }
}


const signOut = async (event) => {
  const signOutCommand = new GlobalSignOutCommand({
    AccessToken
  })

  try {
    const result = await cognitoClient.send(signOutCommand)
    return {
      status: 200,
      result
    }
  } catch (error) {
    console.log('logout error: ', error)
    throw error
  }
}

const deleteUser = async (event) => {
  const deleteUserCommand = new AdminDeleteUserCommand({
    Username,
    UserPoolId
  })

  try {
    const result = await cognitoClient.send(deleteUserCommand)
    return {
      status: 200,
      result
    }
  } catch (error) {
    console.log('logout error: ', error)
    throw error
  }
}

// const loginPasswordless = async (event) => {
//   return {
//     statusCode: 200,
//     body: JSON.stringify(
//       {
//         message: 'loginPasswordless not implemented',
//         input: event,
//       },
//       null,
//       2
//     ),
//   }
// }

module.exports = {
  registerAdmin,
  findUser,
  // register,
  // confirmUser,
  signOut,
  // loginPasswordless,
  // login,
  userDetails,
  forgotPasswordInit,
  confirmForgotPassword,
  changePassword,
  deleteUser,
  loginWithMfa,
  respondMfa,
  // getUserMfa,
  // resetPassword,
  respondNewPasswordRequired,
  // resendCode
}
