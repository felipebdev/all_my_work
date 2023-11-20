/** USED FOR AXIOS */
export const GET_ALL_CONTENTS_QUERY_AXIOS = `
query(
    $page: Float,
    $limit: Float,
    $title: String
    $module_id: String
    ) {
    contents: getAllContents(
        filters: {
            title: $title
            module_id: $module_id
        }
        pagination: { page: $page, limit: $limit }
    ) {
        total
        data {
            id
            title
            vertical_image
            horizontal_image
            author {
                name_author
                author_photo_url
            }
            module {
                name
                course_id
                courses {
                    name
                }
            }
            author_id
            authorOld:author_id
            is_published
            contentType
            updated_at
            created_at
        }
    }
}`;

export const GET_CONTENT_BY_ID_QUERY_AXIOS = `
query($id: String!) {
  content: getContentById(id: $id) {
    id
    title
    subtitle
    description
    is_published
    vertical_image
    horizontal_image
    author_id
    duration
    section_id
    course_id
    module_id
    module {
      course_id
    }
    order_content
    started_at
    form_delivery
    frequency
    delivery_model
    delivered_at
    delivery_option
    contentType
    contentUrl
    hashtags
    useExternalOAuthToken
    widgets {
        position
        type
        video_url
        audio_url
        image_url
        external_link_url
        file_id
        html
        use_oauth_external_token
        external_link_title
        File {
            name
            id
            storage_link
        }
        text_type
        text
        alert_title
        alert_description
        table_data {
            rows
            backgroundTitle
            backgroundBody
        }
    }
  }
}`;
