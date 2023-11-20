/** USED FOR AXIOS */
export const ALL_SECTIONS_QUERY_AXIOS = `
query($page: Float, $limit: Float, $platform_id: String, $title: String, $published: Boolean) {
    sections: retrieveSectionByParams(
        filters: {
            platform_id: $platform_id
            title: $title
            published: $published
        }
        pagination: { page: $page, limit: $limit }
    ) {
        data {
			id
            title
            thumb_vertical
            thumb_horizontal
            section_items {
                type
                position
                item_id
            }
            published
            platform_id
        }
        total
    }
}`;

export const GET_SECTION_BY_ID_QUERY_AXIOS = `
query($id: String!) {
    section: retrieveSectionById(
       id: $id
    ) {
        id
        title
        thumb_vertical
        thumb_horizontal
        published
        platform_id
        description
        section_items {
            position
            section_id
            type
            item_id
            item_data {
                id
                title
                subtitle
                description
                hashtags
                order_content
                is_published
                vertical_image
                horizontal_image
                deleted_at
                started_at
                form_delivery
                frequency
                delivery_model
                delivery_option
                delivered_at
                duration
                useExternalOAuthToken
                contentType
                contentUrl
                widgets {
                    position
                    type
                    video_url
                    audio_url
                    image_url
                    external_link_url
                    external_link_title
                    file_id
                    File {
                        id
                        name
                        formatType
                        size
                        storage_link
                        platform_id
                        producer_id
                        created_at
                        updated_at
                    }
                    html
                    use_oauth_external_token
                    contentId
                    text_type
                    text
                    alert_title
                    alert_description
                    table_data {
                        rows
                        backgroundTitle
                        backgroundBody
                    }
                    platform_id
                    producer_id
                    created_at
                    updated_at
                }
                section_id
                course_id
                module_id
                name
                active
                platform_id
                author_id
                created_at
                updated_at
                has_offer_link
                offer_link
                is_calendar
                calendar_month
                calendar_year
                is_experience
                module_visualization
            }
            created_at
            updated_at
        }
    }
}`;


export const GET_SECTION_COVER_BY_ID_QUERY_AXIOS = `
query($id: String!) {
    section: retrieveSectionById(
       id: $id
    ) {
        id
        title
        thumb_vertical
        thumb_horizontal
        published
    }
}`;