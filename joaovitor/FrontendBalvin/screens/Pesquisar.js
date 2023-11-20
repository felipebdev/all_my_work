import { StyleSheet, Text, View, Image, ScrollView, ActivityIndicator } from 'react-native'
import React, { useEffect, useState } from 'react'
import { TextInput } from 'react-native-gesture-handler'
import Usercard from './Cards/Usercard'
import { width } from 'deprecated-react-native-prop-types/DeprecatedImagePropType'

const Pesquisar = ({ navigation }) => {
  const [keyword, setKeywords] = useState("")
  const [loading, setLoading] = useState(false)
  const [data, setData] = useState([])
  const [error, setError] = useState(null)

  const getallusers = async () => {
    if (keyword.length > 0) {
        setLoading(true)
        fetch('http://192.168.0.54:3000/procuraruser', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ keyword: keyword })
        })
            .then(res => res.json())
            .then(data => {
                // console.log(data)
                if (data.error) {
                    setData([])
                    setError(data.error)
                    setLoading(false)
                }
                else if (data.message == 'Usuário Encontrado') {
                    setError(null)
                    setData(data.user)
                    setLoading(false)
                }
            })
            .catch(err => {
                setData([])
                setLoading(false)
            })
    }
    else {
        setData([])
        setError(null)
    }
}

useEffect(() => {
    getallusers()
}, [keyword])


  return (
    <View style={{width: '100%', height: '100%'}}>


      <View style={{width: '100%', flexDirection: 'row', backgroundColor: 'white'}}>
        <Image source={require('../images/Search.png')} style={{width: 20, height: 20, marginTop: 15, marginStart: 5}}/>
     <TextInput placeholder="Pesquisar..." style={{width: '90%', height: 50, backgroundColor:'white', padding: 10}}
     onChangeText={(text) => setKeywords(text)}/>
     </View>

    {
      loading ?
      <ActivityIndicator size="large" color="white" style={{marginTop: 10}}/>
      :
      <>
        {
          error ?
          <Text style={{fontSize: 20, color: 'white', textAlign: 'center', marginTop: 10}}>{error}</Text>
          :

          <ScrollView>
            {
              data.map((item, index) => {
                return <Usercard key={item.nome} user={item}
                navigation={navigation}
                />
              })
            }
          </ScrollView>
        }
      </>
    }
   
    </View>
  )
}

export default Pesquisar

const styles = StyleSheet.create({})
