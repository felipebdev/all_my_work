/** USED FOR AXIOS */
export const UPDATE_CONTENT_AUTHOR_MUTATION_AXIOS = `
mutation($id: String!, $author_id: String) {
    content: updateContent(id: $id, data: { author_id: $author_id }) {
        id
        title
    }
}`;

export const UPDATE_CONTENT_STATUS_MUTATION_AXIOS = `
mutation($id: String!, $is_published: Boolean!) {
    content: updateContent(id: $id, data: { is_published: $is_published }) {
        id
        title
        is_published
    }
}`;

export const UPDATE_CONTENT_ORDER_MUTATION_AXIOS = `
mutation($id: String!, $order_content: Int!, $module_id: String) {
  content: updateContent(id: $id, data: { order_content: $order_content, module_id: $module_id }) {
    id
    order_content
    module_id
  }
}`;

export const DELETE_CONTENT_MUTATION_AXIOS = `
mutation($id: String!) {
  content: deleteContent(id: $id) {
    id
    title
  }
}`;

export const CREATE_CONTENT_MUTATION_AXIOS = `
mutation(
  $title: String!
  $subtitle: String
  $description: String
  $hashtags: [String!]
  $order_content: Int! = 0
  $is_published: Boolean = true
  $vertical_image: String = "https://las.xgrow.com/background-default.png"
  $horizontal_image: String = "https://las.xgrow.com/background-default.png"
  $started_at: DateTime
  $author_id: String!
  $section_id: String
  $course_id: String
  $module_id: String
  $form_delivery: EFormDelivery!
  $frequency: Int
  $delivery_model: EDeliveryModel!
  $delivery_option: EDeliveryOption!
  $delivered_at: DateTime
  $duration: Int = 0
  $contentType: EContentType!
  $useExternalOAuthToken: Boolean!
  $contentUrl: String
  $widgets: [WidgetFieldsInput!]
) {
  content: createContent(
    data: {
      title: $title
      subtitle: $subtitle
      description: $description
      hashtags: $hashtags
      order_content: $order_content
      is_published: $is_published
      vertical_image: $vertical_image
      horizontal_image: $horizontal_image
      author_id: $author_id
      section_id: $section_id
      course_id: $course_id
      module_id: $module_id
      started_at: $started_at
      form_delivery: $form_delivery
      frequency: $frequency
      delivery_model: $delivery_model
      delivery_option: $delivery_option
      delivered_at: $delivered_at
      duration: $duration
      contentType: $contentType
      useExternalOAuthToken: $useExternalOAuthToken
      contentUrl: $contentUrl
      Widgets: $widgets
    }
  ) {
    id
    title
    author_id
    author {
      id
      name_author
    }
    subtitle
    description
    order_content
    is_published
    vertical_image
    horizontal_image
    section_id
    course_id
    module_id
    started_at
    form_delivery
    frequency
    delivery_model
    delivered_at
    duration
    created_at
    updated_at
  }
}`;


export const UPDATE_CONTENT_MUTATION_AXIOS = `
mutation(
  $id: String!
  $title: String
  $subtitle: String
  $description: String
  $hashtags: [String!]
  $order_content: Int = 0
  $is_published: Boolean = false
  $vertical_image: String = "https://las.xgrow.com/background-default.png"
  $horizontal_image: String = "https://las.xgrow.com/background-default.png"
  $started_at: DateTime
  $author_id: String
  $section_id: String
  $course_id: String
  $module_id: String
  $form_delivery: EFormDelivery
  $frequency: Int
  $delivery_model: EDeliveryModel
  $delivery_option: EDeliveryOption
  $delivered_at: DateTime
  $duration: Int = 0
  $contentType: EContentType
  $contentUrl: String
  $useExternalOAuthToken: Boolean!
  $widgets: [WidgetFieldsInput!]
) {
  content: updateContent(
    id: $id
    data: {
        title: $title
        subtitle: $subtitle
        description: $description
        hashtags: $hashtags
        order_content: $order_content
        is_published: $is_published
        vertical_image: $vertical_image
        horizontal_image: $horizontal_image
        author_id: $author_id
        section_id: $section_id
        course_id: $course_id
        module_id: $module_id
        started_at: $started_at
        form_delivery: $form_delivery
        frequency: $frequency
        delivery_model: $delivery_model
        delivery_option: $delivery_option
        delivered_at: $delivered_at
        duration: $duration
        contentType: $contentType
        contentUrl: $contentUrl
        useExternalOAuthToken: $useExternalOAuthToken
        Widgets: $widgets
    }
  ) {
    id
    title
  }
}`;


export const UPDATE_DELIVERY_CONTENT_MUTATION_AXIOS = `
mutation(
  $id: String!
  $delivered_at: DateTime
  $delivery_model: EDeliveryModel
  $frequency: Int
  $started_at: DateTime
  $delivery_option: EDeliveryOption
  $form_delivery: EFormDelivery
) {
  content: updateContent(
    id: $id
    data: {
      delivered_at: $delivered_at
      delivery_model: $delivery_model
      frequency: $frequency
      started_at: $started_at
      delivery_option: $delivery_option
      form_delivery: $form_delivery
    }
  ) {
    id
    title
  }
}`;
