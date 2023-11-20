import { StyleSheet, Text, View, Image, TouchableOpacity } from 'react-native'
import React, { useEffect } from 'react'
import { StatusBar } from 'expo-status-bar';
import { AntDesign } from '@expo/vector-icons';
import { FontAwesome } from '@expo/vector-icons';
import { ScrollView } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Gosteiecomentario from './Publicacao/Gosteiecomentario'


const Homescreen = ({navigation}) => {
  const [userdata, setUserdata] = React.useState(null)
  useEffect(() => {
      AsyncStorage.getItem('user')
          .then(data => {
              // console.log('async userdata ', data)
              setUserdata(JSON.parse(data))
          })
          .catch(err => alert(err))
  }, [])

  console.log('userdata', userdata)

  return (
    <View>
      <ScrollView>
        <StatusBar hidden={true}/>
        <View style={{flexDirection: 'row', width: '100%', marginTop: 20, alignItems: 'center'
        , justifyContent:'center'}}>

        <TouchableOpacity  style={{marginEnd: 80}} onPress={() => navigation.navigate('Addpost')}>
        <Image source={require('../images/addplus.png')}/>
        </TouchableOpacity>

        <Image source={require('../images/balvintext.png')} style={{width: 120, height: 70}}/>

        <TouchableOpacity onPress={() => navigation.navigate('Todoschats')} style={{marginStart: 80}}>
        <Image source={require('../images/chat.png')}/>
        </TouchableOpacity>

        </View>
     <Gosteiecomentario/>
     </ScrollView>
    </View>

  )
}

export default Homescreen

const styles = StyleSheet.create({})