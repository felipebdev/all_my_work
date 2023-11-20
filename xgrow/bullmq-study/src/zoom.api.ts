import axios from 'axios'

const BASE_URL = 'https://zoom.us/oauth/token'

const ACCOUNT_ID = 'Ptig7J-4TYOC7ZUR079D-w'

const HEADERS = {
    Authorization: `Basic RXZ3Y3FVRmlRT2lBbGRxeEpzOThjdzpOejBaOXl4V1prYjlrV1lxTUt4SlRPNEtETzBoMFFLWA==`,
}

const params = {
    grant_type: 'account_credentials',
    account_id: ACCOUNT_ID
}


const authenticate = async () => {



    const {data} = await axios.post(
        `https://zoom.us/oauth/token`,
        {},
        {
            headers: HEADERS,
            params
        }
    )

    console.log('auth', data)

    return data
}

const createMeeting = async () => {

    const {access_token} = await authenticate()

    // const user = {
    //     "action": "create",
    //     "user_info": {
    //       "email": "felipinho@example.com",
    //       "first_name": "Felipee",
    //       "last_name": "Bonazzii",
    //       "display_name": "Bonazzi Dev",
    //       "password": "if42!LfH@",
    //       "type": 1,
    //       "feature": {
    //         "zoom_phone": false,
    //       },
    //     }
    //   }

// const meeting = {
//   "assistant_id": "kFFvsJc-Q1OSxaJQLvaa_A",
//   "host_email": "jchill@example.com",
//   "id": 92674392836,
// //   "registration_url": "https://example.com/meeting/register/7ksAkRCoEpt1Jm0wa-E6lICLur9e7Lde5oW6",
//   "agenda": "Testando Meeting API",
//   "created_at": "2022-03-25T07:29:29Z",
//   "duration": 60,
//   "h323_password": "123456",
// //   "join_url": "https://example.com/j/11111",
// //   "occurrences": [
// //     {
// //       "duration": 60,
// //       "occurrence_id": "1648194360000",
// //       "start_time": "2022-03-25T07:46:00Z",
// //       "status": "available"
// //     }
// //   ],
// //   "password": "123456",
// //   "pmi": "97891943927",
// //   "pre_schedule": false,
// //   "recurrence": {
// //     "end_date_time": "2022-04-02T15:59:00Z",
// //     "end_times": 7,
// //     "monthly_day": 1,
// //     "monthly_week": 1,
// //     "monthly_week_day": 1,
// //     "repeat_interval": 1,
// //     "type": 1,
// //     "weekly_days": "1"
// //   },
//   "settings": {
//     "meeting_invitees": [
//       {
//         "email": "felipebonazzi1@gmail.com"
//       },
//       {
//         "email":  "jonasdiel@xgrow.com"
//       }
//     ],
//     "allow_multiple_devices": true,
//     // "alternative_hosts": "jchill@example.com;thill@example.com",
//     // "alternative_hosts_email_notification": true,
//     // "alternative_host_update_polls": true,
//     // "approval_type": 0,
//     // "approved_or_denied_countries_or_regions": {
//     //   "approved_list": [
//     //     "CX"
//     //   ],
//     //   "denied_list": [
//     //     "CA"
//     //   ],
//     //   "enable": true,
//     //   "method": "approve"
//     // },
//     // "audio": "telephony",
//     "audio_conference_info": "test",
//     // "authentication_domains": "example.com",
//     // "authentication_exception": [
//     //   {
//     //     "email": "jchill@example.com",
//     //     "name": "Jill Chill",
//     //     "join_url": "https://example.com/s/11111"
//     //   }
//     // ],
//     // "authentication_name": "Sign in to Zoom",
//     // "authentication_option": "signIn_D8cJuqWVQ623CI4Q8yQK0Q",
//     // "auto_recording": "cloud",
//     // "breakout_room": {
//     //   "enable": true,
//     //   "rooms": [
//     //     {
//     //       "name": "room1",
//     //       "participants": [
//     //         "felipebonazzi1@gmail.com"
//     //       ]
//     //     }
//     //   ]
//     // },
//     // "calendar_type": 1,
//     "close_registration": false,
//     "contact_email": "felipebdev@gmail.com",
//     "contact_name": "Jill Chill",
//     "custom_keys": [
//       {
//         "key": "key1",
//         "value": "value1"
//       }
//     ],
//     "email_notification": true,
//     "encryption_type": "enhanced_encryption",
//     "focus_mode": true,
//     // "global_dial_in_countries": [
//     //   "US"
//     // ],
//     // "global_dial_in_numbers": [
//     //   {
//     //     "city": "New York",
//     //     "country": "US",
//     //     "country_name": "US",
//     //     "number": "+1 1000200200",
//     //     "type": "toll"
//     //   }
//     // ],
//     "host_video": true,
//     "jbh_time": 0,
//     "join_before_host": true,
//     // "language_interpretation": {
//     //   "enable": true,
//     //   "interpreters": [
//     //     {
//     //       "email": "interpreter@example.com",
//     //       "languages": "US,FR"
//     //     }
//     //   ]
//     // },
//     "meeting_authentication": false,
//     "mute_upon_entry": false,
//     "participant_video": false,
//     "private_meeting": false,
//     "registrants_confirmation_email": true,
//     "registrants_email_notification": true,
//     "registration_type": 1,
//     "show_share_button": true,
//     "use_pmi": false,
//     "waiting_room": false,
//     "watermark": false,
//     "host_save_video_order": true
//   },
//   "start_time": "2022-03-25T07:29:29Z",
//   "timezone": "America/Los_Angeles",
//   "topic": "Testando API MEETING",
//   "type": 2
// }

    const meeting = {
        "topic": "Felipe Bonazzi Topico",
        "type": 2,
        "start_time": "2023-10-20T22: 15: 00",
        "duration": "70",
        "timezone": "America/Araguaina",
        "agenda": "Meeting Name Bonazzi",
        // "password":"12345",
        "settings": {
            "meeting_invitees": [
                {"email": "felipebonazzi1@gmail.com"},
                {"email": "jonasdiel@xgrow.com"},
                {"email": "felipebdev@gmail.com"}
            ],
            "meeting_authentication": false,

        }
    }

    const {data} = await axios.post(
        'https://api.zoom.us/v2/users/me/meetings',
        meeting,
        {
            headers: {
                Authorization: `Bearer ${access_token}`
            }
        }
    )

    // const {id} = data

    // const registrants = {
    //     "first_name": "Felipe",
    //     "last_name": "Bonazzi",
    //     "email": "felipebonazzi1@gmail.com",
    //     // "address": "1800 Amphibious Blvd.",
    //     // "city": "Mountain View",
    //     // "state": "CA",
    //     // "zip": "94045",
    //     // "country": "US",
    //     // "phone": "5550100",
    //     "comments": "Looking forward to the discussion.",
    //     "custom_questions": [
    //       {
    //         "title": "What do you hope to learn from this?",
    //         "value": "Look forward to learning how you come up with new recipes and what other services you offer."
    //       }
    //     ],
    //     // "industry": "Food",
    //     // "job_title": "Chef",
    //     // "no_of_employees": "1-20",
    //     // "org": "Cooking Org",
    //     // "purchasing_time_frame": "1-3 months",
    //     // "role_in_purchase_process": "Influencer",
    //     // "language": "en-US",
    //     "auto_approve": true
    //   }

    // const {data: registrantData} = await axios.post(
    //     `https://api.zoom.us/v2/meetings/${id}/registrants`,
    //     registrants,
    //     {
    //         headers: {
    //             Authorization: `Bearer ${access_token}`
    //         }
    //     }
    // )

    console.log('meeting', data)
    // console.log('registrant', registrantData.response.data.errors)

    return data
}


// const scheduleMeeting = async () => {

//     console.log('Headers', HEADERS)

//     const {data} = await axios.post(
//         BASE_URL,
//         DATA,
//         {
//             headers: HEADERS
//         }
//     )

//     console.log('data', data)

//     return data
// }
createMeeting()
    .then((response) => console.log('res', response))
    .catch((err) => {
        console.log('err', err)
        process.exit(1)
    })