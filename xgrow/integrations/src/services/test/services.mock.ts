import { Payload } from '../../job'

export const wiseNotasPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'onApprovePayment',
      id: 794,
      integration: {
        api_account: null,
        api_key: '576d561e-6477-4cf5-b93f-xxxxxxxxx',
        api_secret: null,
        api_webhook: 'https://wisenotas.com.br/api/hooks/xgrow',
        id: 434,
        metadata: {
          process_after_days: '30'
        },
        type: 'wisenotas'
      },
      metadata: {
        days_never_accessed: null
      },
      planIds: [
        6631,
        6670,
        6673,
        6674,
        6675,
        6719,
        6930,
        6931,
        6932,
        6933,
        6934,
        6935,
        6936,
        6937,
        6941,
        6943,
        6945,
        6948,
        6949,
        6965,
        6995
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      client_address: 'Avenida Paulista',
      client_city: 'São Paulo',
      client_cnpj: '37.676.810/0001-72',
      client_company_name: 'MARCAL SERVICOS DIGITAIS LTDA',
      client_complement: undefined,
      client_cpf: undefined,
      client_holder_name: 'Any Client Holder Name',
      client_type_person: 'J',
      payment_customer_value: 3103.07,
      payment_date: '2022-12-30',
      payment_installment_number: 1,
      payment_installments: 1,
      payment_model: 'P',
      payment_order_code: '1077688056',
      payment_plans: [
        {
          id: 6965,
          plan: 'O Pior Ano da Sua Vida 2023 | SPR',
          price: 2497,
          price_plus_fees: 2497,
          type: 'product'
        },
        {
          id: 6958,
          plan: 'Método IP 12 + Guia definitivo para acabar das dívidas + 12 livros do Pablo Marçal  em E-book + Networking PRO',
          price: 697,
          price_plus_fees: 697,
          type: 'order_bump',
          coproducers: [
            {
              name: 'John Doe',
              issue_invoice: true,
              invoice_percent: 20,
              address: 'Avenida Paulista',
              city: 'São Paulo',
              cnpj: '37.676.810/0001-72',
              company_name: 'MARCAL SERVICOS DIGITAIS LTDA',
              complement: null,
              cpf: null,
              district: 'Bela Vista',
              fantasy_name: 'MARCAL SERVICOS DIGITAIS LTDA',
              number: '171',
              state: 'SP',
              type_person: 'J',
              zipcode: '01311-904'
            }
          ]
        }
      ],
      payment_plans_value: 3194,
      payment_price: '3194.00',
      payment_status: null,
      payment_type: 'pix',
      subscriber_birthday: null,
      subscriber_document_number: '03496861576',
      subscriber_document_type: 'CPF',
      subscriber_email: 'paulasxxxxx@gmail.com',
      subscriber_id: 659007,
      subscriber_name: 'Ana Paula de Souza santos',
      subscriber_phone: '(71) 99999999',
      subscriber_plan_id: 6965,
    }
  }
}

export const webhookPayload: Payload = {
  header: {
    app: {
      action: 'bindTriggerWebhook',
      app_id: 434,
      event: 'onApprovePayment',
      id: 794,
      integration: {
        api_account: null,
        api_key: '576d561e-6477-4cf5-b93f-xxxxxxxxx',
        api_secret: null,
        api_webhook: 'anywebhookpayload.com',
        id: 434,
        metadata: {
          process_after_days: '30'
        },
        type: 'webhook'
      },
      metadata: {
        days_never_accessed: null
      },
      planIds: [
        6631
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      any: 'anydata',
      transaction_plans: [1234,12345],
      payment_date: '12-24-1998'
    }
  }
}

export const cademiPayload: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'onApprovePayment',
      id: 794,
      integration: {
        api_account: null,
        api_key: '576d561e-6477-4cf5-b93f-xxxxxxxxx',
        api_secret: null,
        api_webhook: 'cademi.com',
        id: 434,
        metadata: {
          process_after_days: '30'
        },
        type: 'cademi'
      },
      metadata: {
        days_never_accessed: null
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_name: 'subscriberName',
      subscriber_document_number: '507.834.268-01',
      subscriber_email: 'any@email.com',
      subscriber_phone: '19982867377',
      payment_order_code: 'anycode',
      payment_plans: [
        {
          id: 1
        },
        {
          id: 2
        }
      ]
    }
  }
}

export const kajabiPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'onApprovePayment',
      id: 794,
      integration: {
        api_account: null,
        api_key: '576d561e-6477-4cf5-b93f-xxxxxxxxx',
        api_secret: null,
        api_webhook: 'cademi.com',
        id: 434,
        metadata: {
          product_webhook: 'anyvalue?anyvalue'
        },
        type: 'kajabi'
      },
      metadata: {
        product_webhook: 'cademi.com'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_id: 1234,
      subscriber_name: 'subscriberName',
      subscriber_email: 'subscriber@email.com'
    }
  }
}

export const voxuyPayloadMock: Payload = {
  header: {
    app: {
      action: 'bindTriggerWebhook',
      app_id: 434,
      event: 'onApprovePayment',
      id: 794,
      integration: {
        api_account: null,
        api_key: '576d561e-6477-4cf5-b93f-xxxxxxxxx',
        api_secret: null,
        api_webhook: 'voxuy.com',
        id: 434,
        metadata: {
          planId: '1'
        },
        type: 'kajabi'
      },
      metadata: {
        product_webhook: 'cademi.com'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_name: 'subscriberName',
      subscriber_document_number: '507.834.268-01',
      subscriber_email: 'subscriber@email.com',
      subscriber_phone: '19982837474',
      payment_type: 'gratuito',
      payment_status: 'paid',
    }
  }
}

// export const expoPayloadMock: Payload = {
//   header: {
//     app: {
//       action: 'bindPushNotification',
//       app_id: 434,
//       event: 'onApprovePayment',
//       id: 794,
//       integration: {
//         api_account: null,
//         api_key: '576d561e-6477-4cf5-b93f-xxxxxxxxx',
//         api_secret: null,
//         api_webhook: 'expo.com',
//         id: 434,
//         metadata: {
//           expoTokens: ['anytoken1', 'anytoken2'],
//           messageTitle: 'anytitle',
//           messageBody: 'anybody',
//           messageData: 'anydata'
//         },
//         type: 'expo'
//       },
//       metadata: {
//       },
//       planIds: [
//         1,
//         2
//       ],
//       platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
//     },
//     date: '2022-12-30 08:54:23'
//   },
//   payload: {
//     data: {}
//   }
// }

export const builderallPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'onApprovePayment',
      id: 794,
      integration: {
        api_key: 'any-api-token',
        id: 434,
        type: 'builderall'
      },
      metadata: {
        list: '1,2,3,4,5'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_email: 'any@email.com'
    }
  }
}

export const leadloversPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'onApprovePayment',
      id: 794,
      integration: {
        api_key: 'any-api-token',
        id: 434,
        type: 'leadlovers',
        api_webhook: 'leadlovers.com'
      },
      metadata: {
        tags: 'any, any2',
        machineCode: '1234',
        sequenceCode: '12345',
        levelCode: '123456'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_email: 'contact@email.com',
      subscriber_name: 'anyName',
      subscriber_phone: '19982867777',
      subscriber_city: 'Indaiatuba',
      subscriber_state: 'SP'
    }
  }
}

export const mauticPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'onApprovePayment',
      id: 794,
      integration: {
        api_key: 'any-api-token',
        id: 434,
        type: 'mautic',
        api_account: 'any-api-account',
        api_webhook: 'mautic.com'
      },
      metadata: {
        list: '9191'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_email: 'subscriber@email.com',
      subscriber_name: 'John Doe',
      subscriber_phone: '19982867373'
    }
  }
}

export const pipedrivePayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'onApprovePayment',
      id: 794,
      integration: {
        api_key: 'any-api-token',
        id: 434,
        type: 'pipedrive',
        api_account: 'any-api-account',
      },
      metadata: {},
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_email: 'subscriber@email.com',
      subscriber_name: 'John Doe',
      subscriber_phone: '19982867373'
    }
  }
}

export const smartnotasPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'onApprovePayment',
      id: 794,
      integration: {
        api_key: 'any-api-token',
        id: 434,
        type: 'smartnotas',
        api_account: 'any-api-account',
        api_webhook: 'smartnotas.com',
        metadata: {
          process_after_days: '3'
        }
      },
      planIds: [
        122
      ],
      platform_id: 'anyplatformid'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      client_cpf: '507.834.268-01',
      client_cnpj: undefined,
      client_fantasy_name: 'John Doe',
      subscriber_name: 'John Doe',
      subscriber_document_number: '55.095.654-3',
      subscriber_email: 'subscriber@email.com',
      subscriber_phone: '19982867373',
      payment_status: 'paid',
      payment_order_code: 'anyordercode',
      payment_date: '12-24-2021',
      payment_plans: [
        {
          id: 122,
          plan: 'anyplanname',
          price: 100,
          type: 'anypaymenttype'
        }
      ],
      payment_installment_number: 'anyname'
    }
  }
}

export const rdstationPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'anyevent',
      id: 794,
      integration: {
        api_key: 'any-api-token',
        id: 434,
        type: 'rdstation',
        api_account: 'any-api-account',
      },
      metadata: {
        tags: ['tag1, tag2']
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_email: 'subscriber@email.com',
      subscriber_name: 'John Doe',
      subscriber_phone: '19982867373',
      subscriber_city: 'Indaiatuba',
      subscriber_state: 'SP',
      subscriber_country: 'Brasil'
    }
  }
}

export const memberkitPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'onCreateLead',
      id: 794,
      integration: {
        api_key: 'any-api-token',
        id: 434,
        type: 'memberkit',
        api_account: 'any-api-account',
      },
      // metadata: {
      //   list: '1'
      // },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_email: 'subscriber@email.com',
      subscriber_name: 'John Doe',
      subscriber_phone: '19982867373',
      subscriber_city: 'Indaiatuba',
      subscriber_state: 'SP',
      subscriber_country: 'Brasil',
      subscriber_document_number: '507.834.268-00'
    }
  }
}

export const hubspotPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'anyevent',
      id: 794,
      integration: {
        api_key: 'any-api-token',
        id: 434,
        type: 'hubspot',
        api_account: 'any-api-account',
      },
      metadata: {
        list: '1'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_email: 'subscriber@email.com',
      subscriber_name: 'John Doe',
      subscriber_phone: '19982867373',
      subscriber_city: 'Indaiatuba',
      subscriber_state: 'SP',
      subscriber_country: 'Brasil',
      subscriber_document_number: '507.834.268-00',
      subscriber_street: 'anystreet',
      subscriber_number: '123',
      subscriber_zipcode: '13340501'
    }
  }
}

export const activecampaignPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'anyevent',
      id: 794,
      integration: {
        api_key: 'any-api-token',
        id: 434,
        type: 'activecampaign',
        api_account: 'any-api-account',
        api_webhook: 'activecampaign.com'
      },
      metadata: {
        tags: ['777'],
        list: '1',
        change_card_field: 'anyid'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_email: 'subscriber@email.com',
      subscriber_name: 'John Doe',
      subscriber_phone: '19982867373',
      subscriber_city: 'Indaiatuba',
      subscriber_state: 'SP',
      subscriber_country: 'Brasil',
      subscriber_document_number: '507.834.268-00',
      subscriber_street: 'anystreet',
      subscriber_number: '123',
      subscriber_zipcode: '13340501',
      transaction_origin: 'any',
      change_card_url: 'url'
    }
  }
}

export const infusionPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'anyevent',
      id: 794,
      integration: {
        api_key: 'any-api-token',
        id: 434,
        type: 'infusion',
        api_account: 'any-api-account',
      },
      metadata: {
        tags: ['777'],
        list: '1'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_name: 'John Doe',
      subscriber_email: 'subscriber@email.com',
      subscriber_phone: '19982867373'
    }
  }
}

export const octadeskPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'anyevent',
      id: 794,
      integration: {
        api_secret: 'any-api-token',
        api_account: 'any-api-account',
        api_key: 'any-api-key',
        id: 434,
        type: 'octadesk',
      },
      metadata: {
        tags: ['777'],
        list: '1'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_id: 'subscriberId',
      subscriber_name: 'John Doe',
      subscriber_email: 'subscriber@email.com',
      subscriber_phone: '19982867373'
    }
  }
}

export const mailchimpPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'anyevent',
      id: 794,
      integration: {
        api_secret: 'any-api-token',
        api_account: 'any-api-account',
        api_key: 'apikey-mailchimpdatacentercode',
        id: 434,
        type: 'mailchimp',
      },
      metadata: {
        tags: ['777'],
        list: '1'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_id: 'subscriberId',
      subscriber_name: 'John Doe',
      subscriber_email: 'subscriber@email.com',
      subscriber_phone: '19982867373'
    }
  }
}

export const enotasPayloadMock: Payload = {
  header: {
    app: {
      action: null,
      app_id: 434,
      event: 'anyevent',
      id: 794,
      integration: {
        api_key: 'apikey-mailchimpdatacentercode',
        id: 434,
        type: 'enotas',
        metadata: {
          process_after_days: 2
        },
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      subscriber_name: 'John Doe',
      subscriber_email: 'subscriber@email.com',
      subscriber_phone: '19982867373' ,
      subscriber_document_number: '507.834.268-01',
      subscriber_zipcode: '13340-501',
      subscriber_street: 'Rua Any',
      subscriber_number: '245',
      subscriber_district: 'Bairro',
      subscriber_comp: 'Any Comp',
      subscriber_city: 'Indaiatuba',
      payment_date: '12-24-2022',
      client_city: 'Campinas',
      payment_plans_value: 1000,
      payment_type: 'boleto',
      payment_plans: [
        {
          id: 'anyproductid',
          plan: 'anyproductname',
          price: 1000
        }
      ]
    }
  }
}

export const notazzPayloadMock: Payload =
{
  header: {
    date: '2023-02-07 16:06:52',
    app: {
      id: 571,
      app_id: 310,
      platform_id: 'c70e6462-4a6c-4781-bfff-bd4f8dd76c09',
      event: 'onApprovePayment',
      action: null,
      planIds: [
        12345
      ],
      integration: {
        id: 310,
        type: 'notazz',
        api_key: 'mynotazzapikey',
        api_account: null,
        api_secret: null,
      }
    }
  },
  payload: {
    data: {
      client_type_person: 'J',
      client_cpf: null,
      client_cnpj: '40089137000189',
      client_fantasy_name: null,
      client_company_name: 'Plataforma de testes',
      client_address: 'Rua Ipiranga',
      client_number: '111',
      client_complement: '',
      client_district: 'Vila Barros',
      client_city: 'Barueri',
      client_state: 'SP',
      client_zipcode: '06410-250',
      client_holder_name: 'John Doe Holder',
      subscriber_id: 550653,
      subscriber_plan_id: 4692,
      subscriber_email: 'felipebdev@gmail.com',
      subscriber_name: 'John Doe Xgrow',
      subscriber_phone: '24998786654',
      subscriber_birthday: null,
      subscriber_zipcode: '13340551',
      subscriber_street: 'Alguma Rua',
      subscriber_number: '245',
      subscriber_comp: 'Algum Complemento',
      subscriber_district: 'Algum Bairro',
      subscriber_city: 'Indaiatuba',
      subscriber_state: 'SP',
      subscriber_country: 'BRA',
      subscriber_document_type: 'CPF',
      subscriber_document_number: '17261626058',
      payment_price: 6628.56,
      payment_date: '2023-02-07T19:06:49.718188Z',
      payment_status: 'paid',
      payment_order_code: '1234512345',
      payment_type: 'credit_card',
      payment_installment_number: 1,
      payment_installments: 6,
      payment_model: 'P',
      payment_customer_value: 5638.6,
      payment_plans_value: 12000,
      payment_plans: [
        {
          id: 4692,
          plan: 'Teste Xgrow Notazz',
          type: 'product',
          price: 6000,
          price_plus_fees: 6628.56,
        },

        {
          id: 4693,
          plan: 'Teste Xgrow Notazz 2',
          type: 'product',
          price: 6000,
          price_plus_fees: 6628.56,
        },
      ]
    }
  }
}
